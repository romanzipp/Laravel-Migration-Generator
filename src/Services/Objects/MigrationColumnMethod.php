<?php

namespace romanzipp\MigrationGenerator\Services\Objects;

class MigrationColumnMethod
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed[]
     */
    public $parameters;

    /**
     * @param string $name
     * @param mixed[] $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        [$name, $parameters] = self::simplify($name, $parameters) ?? [$name, $parameters];

        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     * @param mixed[] $parameters
     *
     * @return mixed[]|null
     */
    private static function simplify(string $name, array $parameters): ?array
    {
        if ('bigInteger' === $name) {
            if (count($parameters) < 3) {
                return null;
            }

            // Auto increment & unsigned
            if (true === $parameters[1] && true === $parameters[2]) {
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
