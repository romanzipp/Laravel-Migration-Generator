<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;

class ColumnsConductor
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    /**
     * @var string
     */
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
        $columns = [];

        $doctrineConnection = $this->connection->getDoctrineConnection();
        if (method_exists($doctrineConnection, 'createSchemaManager')) {
            $schemaManager = $doctrineConnection->createSchemaManager();
        } else {
            $schemaManager = $doctrineConnection->getSchemaManager();
        }

        $result = $schemaManager->listTableColumns($this->table);

        foreach ($result as $key => $column) {
            $columns[] = $column;
        }

        return $columns;
    }
}
