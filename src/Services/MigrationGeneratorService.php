<?php

namespace romanzipp\MigrationGenerator\Services;

use Illuminate\Database\Connection;
use Illuminate\Foundation\Application;
use romanzipp\MigrationGenerator\Services\Conductors\ColumnsConductor;
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
     * Get the established database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getDatabaseConnection(): Connection
    {
        return $this->application['db']->connection($this->connection);
    }

    /**
     * Execute the migration generator.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $connection = $this->getDatabaseConnection();

        $tables = (new TablesConductor($connection))->getTables();

        foreach ($tables as $table) {

            $columns = (new ColumnsConductor($connection, $table))->getColumns();
        }
    }
}
