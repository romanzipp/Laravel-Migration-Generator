<?php

namespace romanzipp\MigrationGenerator\Tests\Support\Concerns;

trait CleansUpFiles
{
    protected function cleanUpFiles(): void
    {
        $files = scandir(self::OUTPUT_DIR);

        foreach ($files as $file) {

            $filePath = self::OUTPUT_DIR . '/' . $file;

            if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            unlink($filePath);
        }
    }
}
