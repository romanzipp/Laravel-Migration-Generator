<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;

class TablesConductor
{
    public const LARAVEL_TABLES = [
        'migrations',
    ];

    public const SYSTEM_TABLES_SQLITE = [
        'sqlite_master',
        'sqlite_sequence',
        'sqlite_stat1',
    ];

    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return string[]
     */
    public function getTables(): array
    {
        return array_values($this->getTablesByConnection());
    }

    /**
     * @return string[]
     */
    private function getTablesByConnection(): array
    {
        if ($this->connection instanceof SQLiteConnection) {
            return $this->getTablesForSQLite();
        }

        if ($this->connection instanceof MySqlConnection) {
            return $this->getTablesForMySql();
        }

        try {
            return $this->connection->getSchemaBuilder()->getTableListing(null, false);
        } catch (\LogicException $e) {
            return [];
        }
    }

    /**
     * @return string[]
     */
    private function getTablesForMySql(): array
    {
        $tables = $this->connection->getSchemaBuilder()->getTableListing($this->connection->getDatabaseName(), false);

        return array_filter(
            $tables,
            function ($table) {
                return ! in_array($table, self::LARAVEL_TABLES);
            }
        );
    }

    /**
     * @return string[]
     */
    private function getTablesForSQLite(): array
    {
        $result = $this->connection->select('SELECT NAME FROM sqlite_master WHERE type="table"');

        return array_filter(
            array_map(
                function ($item) {
                    return $item->name;
                },
                $result
            ),
            function ($table) {
                return ! in_array($table, self::LARAVEL_TABLES) && ! in_array($table, self::SYSTEM_TABLES_SQLITE);
            }
        );
    }
}
