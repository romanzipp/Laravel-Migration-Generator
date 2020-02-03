<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Conductors\ColumnsConductor;
use romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;
use romanzipp\MigrationGenerator\Tests\Support\Concerns\CleansUpFiles;

class FileExecutionTest extends TestCase
{
    use CleansUpFiles;

    public function setUp(): void
    {
        parent::setUp();

        $this->cleanUpFiles();
    }

    public function testStoringFile()
    {
        /** @var MigrationGeneratorService $service */
        $service = app(MigrationGeneratorService::class);
        $service();

        $builder = $service->getDatabaseConnection()->getSchemaBuilder();
        $builder->dropAllTables();

        foreach ($service->getMigrations() as $migration) {
            /** @var MigrationGeneratorConductor $migration */

            $path = self::OUTPUT_DIR . '/' . $migration->getFileName();

            require $path;

            $class = $migration->getClassName();

            (new $class)->up();

            $this->assertTrue(
                $builder->hasTable(
                    $migration->getTable()
                )
            );

            $newColumns = (new ColumnsConductor($service->getDatabaseConnection(), $migration->getTable()))->getColumns();
            $originalColumns = $migration->getColumns();

            $this->assertEquals(
                count($originalColumns),
                count($newColumns)
            );

            foreach ($migration->getColumns() as $key => $originalColumn) {

                $this->assertEquals($originalColumn->getType(), $newColumns[$key]->getType());
                $this->assertEquals($originalColumn->getLength(), $newColumns[$key]->getLength());
                $this->assertEquals($originalColumn->getPrecision(), $newColumns[$key]->getPrecision());
                $this->assertEquals($originalColumn->getUnsigned(), $newColumns[$key]->getUnsigned());
                $this->assertEquals($originalColumn->getFixed(), $newColumns[$key]->getFixed());
                $this->assertEquals($originalColumn->getNotnull(), $newColumns[$key]->getNotnull());
                $this->assertEquals($originalColumn->getDefault(), $newColumns[$key]->getDefault());
                $this->assertEquals($originalColumn->getAutoincrement(), $newColumns[$key]->getAutoincrement());
                $this->assertEquals($originalColumn->getComment(), $newColumns[$key]->getComment());
                $this->assertEquals($originalColumn->getName(), $newColumns[$key]->getName());
            }
        }
    }
}
