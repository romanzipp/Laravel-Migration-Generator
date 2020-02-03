<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Conductors\ColumnsConductor;
use romanzipp\MigrationGenerator\Services\Conductors\FileStorageConductor;
use romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor;
use romanzipp\MigrationGenerator\Services\Conductors\TablesConductor;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;

class FileStorageConductorTest extends TestCase
{
    const OUTPUT = __DIR__ . '/Support/files';

    public function setUp(): void
    {
        parent::setUp();

        $this->cleanUpFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanUpFiles();

        parent::tearDown();
    }

    public function testStoringFile()
    {
        config(['migration-generator.path' => __DIR__ . '/Support/files']);

        $migrations = [];

        $service = app(MigrationGeneratorService::class);

        $connection = $service->getDatabaseConnection();

        $tables = (new TablesConductor($connection))->getTables();

        foreach ($tables as $table) {
            /** @var string $table */

            /** @var \Doctrine\DBAL\Schema\Column[] $columns */
            $columns = (new ColumnsConductor($connection, $table))->getColumns();

            $migrations[] = (new MigrationGeneratorConductor($table, $columns))();
        }

        (new FileStorageConductor($migrations))();

        foreach ($migrations as $migration) {
            /** @var MigrationGeneratorConductor $migration */

            $this->assertFileExists(
                __DIR__ . '/Support/files/' . $migration->getFileName()
            );
        }
    }

    protected function cleanUpFiles(): void
    {
        $files = scandir(self::OUTPUT);

        foreach ($files as $file) {

            $filePath = self::OUTPUT . '/' . $file;

            if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            unlink($filePath);
        }
    }
}
