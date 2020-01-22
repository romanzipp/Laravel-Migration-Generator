<?php

namespace romanzipp\MigrationGenerator\Services\Objects;

class MigrationColumnMethod
{
    public $name;

    public $parameters;

    public function __construct(string $name, array $parameters = [])
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }
}
