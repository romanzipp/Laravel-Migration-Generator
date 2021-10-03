<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

class FileStorageConductor
{
    /**
     * @var \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[]
     */
    private $migrations;

    /**
     * @param \romanzipp\MigrationGenerator\Services\Objects\PendingMigration[] $migrations
     */
    public function __construct(array $migrations)
    {
        $this->migrations = $migrations;
    }

    public function __invoke(): void
    {
        foreach ($this->migrations as $migration) {
            $path = sprintf('%s/%s', config('migration-generator.output_path'), $migration->getFileName());

            file_put_contents(
                $path,
                $migration->getStub()
            );
        }
    }
}
