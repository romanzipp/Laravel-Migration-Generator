<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;

class ColumnsConductor
{
    private $connection;

    private $table;

    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Get all doctrine columns for a table.
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getColumns(): array
    {
        $columns = $this->connection->getSchemaBuilder()->getColumnListing($this->table);

        foreach ($columns as $key => $column) {
            $columns[$column] = $this->connection->getDoctrineColumn($this->table, $column);
        }

        return $columns;
    }
}
