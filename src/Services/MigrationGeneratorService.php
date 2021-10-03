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
use romanzipp\MigrationGenerator\Services\Objects\PendingMigration;

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
     * @var \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[]
     */
    private $migrations = [];

    /**
     * @var \romanzipp\MigrationGenerator\Console\Commands\GenerateMigrationsCommand
     */
    private $command;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $connection
     *
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
     *
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
     * @return \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[]
     */
    public function getMigrations(): array
    {
        return $this->migrations;
    }

    /**
     * Order migrations based on existing tables.
     *
     * @param string[] $tables
     * @param \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[] $migrations
     *
     * @return \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[]
     */
    protected function orderMigrations(array $tables, array $migrations): array
    {
        uasort($migrations, function (PendingMigration $a, PendingMigration $b) use ($tables) {
            return array_search($a->getTable(), $tables);
        });

        return $migrations;
    }

    /**
     * Execute the migration generator.
     *
     * @return void
     */
    public function __invoke(): void
    {
        if (null === $this->connection) {
            $this->connection = config('migration-generator.connection');
        }

        $connection = $this->getDatabaseConnection();

        $tables = (new TablesConductor($connection))->getTables();

        /** @var PendingMigration[] $migrations */
        $migrations = [];

        foreach ($tables as $table) {
            /** @var string $table */

            /** @var \Doctrine\DBAL\Schema\Column[] $columns */
            $columns = (new ColumnsConductor($connection, $table))->getColumns();

            $migrations[] = (new MigrationGeneratorConductor($table, $columns))->generateMigration();
        }

        $this->migrations = $this->orderMigrations($tables, $migrations);

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
        if ( ! isset($this->command)) {
            return;
        }

        $callback($this->command);
    }
}
