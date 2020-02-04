<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

class FileStorageConductor
{
    /**
     * @var \romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor[]
     */
    private $migrations;

    public function __construct(array $migrations)
    {
        $this->migrations = $migrations;
    }

    public function __invoke()
    {
        foreach ($this->migrations as $migration) {

            $path = sprintf('%s/%s', config('migration-generator.path'), $migration->getFileName());

            file_put_contents(
                $path,
                $migration->getStub()
            );
        }
    }
}