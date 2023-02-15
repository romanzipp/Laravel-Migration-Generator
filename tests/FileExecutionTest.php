<?php

namespace romanzipp\MigrationGenerator\Tests;

use Doctrine\DBAL\Types\TextType;
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

            (new $class())->up();

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
                $newColumn = $newColumns[$key];

                $this->assertEquals($originalColumn->getType(), $newColumn->getType());

                // MariaDB
                if ( ! $this->isMySQL() && $originalColumn->getType() instanceof TextType) {
                    $this->assertEquals($originalColumn->getLength(), $newColumn->getLength());
                }

                $this->assertEquals($originalColumn->getPrecision(), $newColumn->getPrecision());
                $this->assertEquals($originalColumn->getUnsigned(), $newColumn->getUnsigned());
                $this->assertEquals($originalColumn->getFixed(), $newColumn->getFixed());
                $this->assertEquals($originalColumn->getNotnull(), $newColumn->getNotnull());

                $this->assertEquals(
                    str_replace('\'\'', '', $originalColumn->getDefault()),
                    str_replace('\'\'', '', $newColumn->getDefault())
                );

                $this->assertEquals($originalColumn->getAutoincrement(), $newColumn->getAutoincrement());
                $this->assertEquals($originalColumn->getComment(), $newColumn->getComment());
                $this->assertEquals($originalColumn->getName(), $newColumn->getName());
            }
        }
    }
}
