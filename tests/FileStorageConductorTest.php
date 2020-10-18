<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;
use romanzipp\MigrationGenerator\Services\Objects\PendingMigration;
use romanzipp\MigrationGenerator\Tests\Support\Concerns\CleansUpFiles;

class FileStorageConductorTest extends TestCase
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

        foreach ($service->getMigrations() as $migration) {
            /** @var PendingMigration $migration */

            $path = self::OUTPUT_DIR . '/' . $migration->getFileName();

            $this->assertFileExists($path);
        }
    }

    protected function cleanUpFiles(): void
    {
        $files = scandir(self::OUTPUT_DIR);

        foreach ($files as $file) {
            $filePath = self::OUTPUT_DIR . '/' . $file;

            if ('php' !== pathinfo($filePath, PATHINFO_EXTENSION)) {
                continue;
            }

            unlink($filePath);
        }
    }
}
