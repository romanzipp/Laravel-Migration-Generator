<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;
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
        if ($this->connection instanceof SQLiteConnection) {
            return $this->getTablesForSQLite();
        }

        try {
            return $this->connection->getSchemaBuilder()->getAllTables();
        } catch (LogicException $e) {
            return [];
        }
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
