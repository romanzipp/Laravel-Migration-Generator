<?php

namespace romanzipp\MigrationGenerator\Tests;

use Illuminate\Support\Facades\DB;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;

class GeneratorTest extends TestCase
{
    public function testBasic()
    {
        app(MigrationGeneratorService::class)();
    }
}
