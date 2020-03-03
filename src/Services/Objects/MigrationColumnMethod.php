<?php

namespace romanzipp\MigrationGenerator\Services\Objects;

class MigrationColumnMethod
{
    public $name;

    public $parameters;

    public function __construct(string $name, array $parameters = [])
    {
        [$name, $parameters] = self::simplify($name, $parameters) ?? [$name, $parameters];

        $this->name = $name;
        $this->parameters = $parameters;
    }

    private static function simplify(string $name, array $parameters): ?array
    {
        if ($name === 'bigInteger') {

            if (count($parameters) < 3) {
                return null;
            }

            // Auto increment & unsigned
            if ($parameters[1] === true && $parameters[2] === true) {
                return [
                    'bigIncrements',
                    [
                        $parameters[0],
                    ],
                ];
            }
        }

        return [$name, $parameters];
    }
}
