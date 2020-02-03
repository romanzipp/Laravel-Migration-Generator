<?php

namespace romanzipp\MigrationGenerator\Services;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Application;
use romanzipp\MigrationGenerator\Console\Commands\GenerateMigrationsCommand;
use romanzipp\MigrationGenerator\Services\Conductors\ColumnsConductor;
use romanzipp\MigrationGenerator\Services\Conductors\FileStorageConductor;
use romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor;
use romanzipp\MigrationGenerator\Services\Conductors\TablesConductor;

class MigrationGeneratorService
{
    /**
     * @var Application
     */
    private $application = null;

    /**
     * @var string|null
     */
    private $connection = null;

    /**
     * @var array
     */
    private $migrations = [];

    /**
     * @var GenerateMigrationsCommand
     */
    private $command;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $connection
     * @return $this
     */
    public function connection(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the executing command to enable cli logging.
     *
     * @param \romanzipp\MigrationGenerator\Console\Commands\GenerateMigrationsCommand $command
     * @return $this
     */
    public function command(GenerateMigrationsCommand $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get the established database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getDatabaseConnection(): Connection
    {
        return $this->application['db']->connection($this->connection);
    }

    /**
     * Get all generated migrations.
     *
     * @return MigrationGeneratorConductor[]
     */
    public function getMigrations(): array
    {
        return $this->migrations;
    }

    /**
     * Execute the migration generator.
     *
     * @return void
     */
    public function __invoke(): void
    {
        if ($this->connection === null) {
            $this->connection = config('migration-generator.connection');
        }

        $connection = $this->getDatabaseConnection();

        $tables = (new TablesConductor($connection))->getTables();

        foreach ($tables as $table) {
            /** @var string $table */

            /** @var \Doctrine\DBAL\Schema\Column[] $columns */
            $columns = (new ColumnsConductor($connection, $table))->getColumns();

            $this->migrations[] = (new MigrationGeneratorConductor($table, $columns))();
        }

        $this->commandExec(function (GenerateMigrationsCommand $command) {
            $command->confirm(sprintf('Found %d Migrations. Continue?', count($this->migrations)));
        });

        (new FileStorageConductor($this->migrations))();

        $this->commandExec(function (GenerateMigrationsCommand $command) {
            $command->info('Finished');
        });
    }

    private function commandExec(Closure $callback): void
    {
        if ( ! $this->command) {
            return;
        }

        $callback($this->command);
    }
}
