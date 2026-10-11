<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Doctrine\DBAL\Driver\PDO\Connection as PDOConnection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver as MySQLDriver;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver as SQLiteDriver;
use Illuminate\Database\Connection;
use Illuminate\Database\SQLiteConnection;

class ColumnsConductor
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Get all doctrine columns for a table.
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getColumns(): array
    {
        $columns = [];

        $pdo = $this->connection->getPdo();

        $driver = new class($this->connection instanceof SQLiteConnection ? new SQLiteDriver() : new MySQLDriver(), $pdo) extends AbstractDriverMiddleware {
            /**
             * @var \PDO
             */
            private $pdo;

            public function __construct(Driver $driver, \PDO $pdo)
            {
                parent::__construct($driver);

                $this->pdo = $pdo;
            }

            public function connect(array $params): Driver\Connection
            {
                return new PDOConnection($this->pdo);
            }
        };

        $result = (new DoctrineConnection([], $driver))->createSchemaManager()->listTableColumns($this->table);

        foreach ($result as $key => $column) {
            $columns[] = $column;
        }

        return $columns;
    }
}
