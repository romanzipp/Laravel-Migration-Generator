<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;

class ColumnInfoConductor
{
    /**
     * @var Column
     */
    private $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function getChainedMethods(): array
    {
        $methods = [];

        $methods[] = $this->getMethod();

        return $methods;
    }

    public function getMethod(): ?array
    {
        switch (get_class($this->column->getType())) {

            case IntegerType::class:
                return ['name' => 'integer', 'args' => [$this->column->getName()]];

            case StringType::class:
                return ['name' => 'string', 'args' => [$this->column->getName()]];

            case DateTimeType::class:
                return ['name' => 'datetime', 'args' => [$this->column->getName()]];
        }

        return null;
    }

    public function buildMethodSignature(string $name, array $args)
    {
        $method = '->';
        $method .= $name;
        $method .= '(';

        foreach ($args as $index => $arg) {
            $method .= (is_string($arg) ? sprintf('\'%s\'', $arg) : $arg);
        }

        $method .= ')';

        return $method;
    }

    public function __invoke()
    {
        $line = '$this';

        foreach ($this->getChainedMethods() as $method) {
            $line .= $this->buildMethodSignature($method['name'], $method['args']);
        }

        $line .= ';';

        return $line;
    }
}
