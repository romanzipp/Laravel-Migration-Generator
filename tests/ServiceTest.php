<?php

namespace romanzipp\MigrationGenerator\Tests;

use Illuminate\Database\Connection;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;

class ServiceTest extends TestCase
{
    public function testServiceDatabaseConnection()
    {
        /** @var Connection $connection */
        $connection = app(MigrationGeneratorService::class)->getDatabaseConnection();

        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertInstanceOf(\PDO::class, $connection->getPdo());
    }
}
