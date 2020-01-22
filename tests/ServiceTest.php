<?php

namespace romanzipp\MigrationGenerator\Tests;

use Doctrine\DBAL\Driver\PDOConnection;
use Illuminate\Database\SQLiteConnection;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;

class ServiceTest extends TestCase
{
    public function testServiceDatabaseConnection()
    {
        /** @var SQLiteConnection $connection */
        $connection = app(MigrationGeneratorService::class)->getDatabaseConnection();

        $this->assertInstanceOf(SQLiteConnection::class, $connection);
        $this->assertInstanceOf(PDOConnection::class, $connection->getPdo());
    }
}
