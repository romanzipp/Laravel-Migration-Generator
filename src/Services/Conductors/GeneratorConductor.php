<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Database\Connection;

class GeneratorConductor
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke()
    {
        //
    }
}
