<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use LogicException;

class TablesConductor
{
    const LARAVEL_TABLES = [
        'migrations',
    ];

    const SYSTEM_TABLES_SQLITE = [
        'sqlite_master',
        'sqlite_sequence',
        'sqlite_stat1',
    ];

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTables(): array
    {
        return array_values($this->getTablesByConnection());
    }

    private function getTablesByConnection()
    {
        if ($this->connection instanceof SQLiteConnection) {
            return $this->getTablesForSQLite();
        }

        if ($this->connection instanceof MySqlConnection) {
            return $this->getTablesForMySql();
        }

        try {
            return $this->connection->getSchemaBuilder()->getAllTables();
        } catch (LogicException $e) {
            return [];
        }
    }

    private function getTablesForMySql()
    {
        // Laravel 5.* support
        if (is_callable([$this->connection->getSchemaBuilder(), 'getAllTables'])) {

            $tables = array_map(
                function ($item) {
                    return $item->Tables_in_migration_generator;
                },
                $this->connection->getSchemaBuilder()->getAllTables()
            );

        } else {
            $tables = $this->connection->getDoctrineSchemaManager()->listTableNames();
        }

        return array_filter(
            $tables,
            function ($table) {
                return ! in_array($table, self::LARAVEL_TABLES);
            }
        );
    }

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
