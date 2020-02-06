<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use romanzipp\MigrationGenerator\Services\Objects\PendingMigration;

class MigrationGeneratorConductor
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var \Doctrine\DBAL\Schema\Column[]
     */
    private $columns;

    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * Create a new migration instance.
     *
     * @return PendingMigration
     */
    public function generateMigration(): PendingMigration
    {
        return new PendingMigration(
            $this->table,
            $this->columns
        );
    }

    /**
     * Get columns.
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
