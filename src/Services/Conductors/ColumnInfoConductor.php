<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\BinaryType;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateIntervalType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateTimeTzImmutableType;
use Doctrine\DBAL\Types\DateTimeTzType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\ObjectType;
use Doctrine\DBAL\Types\SimpleArrayType;
use Doctrine\DBAL\Types\SmallIntType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\TimeImmutableType;
use Doctrine\DBAL\Types\TimeType;
use Doctrine\DBAL\Types\VarDateTimeImmutableType;
use Doctrine\DBAL\Types\VarDateTimeType;
use romanzipp\MigrationGenerator\Services\Objects\MigrationColumnMethod;

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

    /**
     * @return MigrationColumnMethod[]
     */
    public function getChainedMethods(): array
    {
        $methods = [];

        $methods[] = $this->getMethod();

        return $methods;
    }

    public function getMethod(): ?MigrationColumnMethod
    {
        switch (get_class($this->column->getType())) {

            case ArrayType::class:
            case BigIntType::class:
                return new MigrationColumnMethod('bigInteger', [$this->column->getName(), $this->column->getPrecision()]);

            case BinaryType::class:
            case BlobType::class:

            case BooleanType::class:
                return new MigrationColumnMethod('boolean', [$this->column->getName()]);

            case DateImmutableType::class:
            case DateIntervalType::class:
            case DateTimeImmutableType::class:

            case DateTimeType::class:
                return new MigrationColumnMethod('datetime', [$this->column->getName()]);

            case DateTimeTzImmutableType::class:
            case DateTimeTzType::class:
            case DateType::class:
            case DecimalType::class:
            case FloatType::class:
            case GuidType::class:

            case IntegerType::class:
                return new MigrationColumnMethod('integer', [$this->column->getName(), $this->column->getPrecision()]);

            case JsonType::class:
            case ObjectType::class:
            case SimpleArrayType::class:
            case SmallIntType::class:

            case StringType::class:
                return new MigrationColumnMethod('string', [$this->column->getName()]);

            case TextType::class:
            case TimeImmutableType::class:
            case TimeType::class:
            case VarDateTimeImmutableType::class:
            case VarDateTimeType::class:
        }

        return null;
    }

    public function buildMethodSignature(string $name, array $parameters)
    {
        $method = '->';
        $method .= $name;
        $method .= '(';

        foreach ($parameters as $index => $parameter) {
            $method .= (is_string($parameter) ? sprintf('\'%s\'', $parameter) : $parameter) . ($index + 1 < count($parameters) ? ', ' : '');
        }

        $method .= ')';

        return $method;
    }

    public function __invoke()
    {
        $line = '$this';

        foreach ($this->getChainedMethods() as $method) {
            $line .= $this->buildMethodSignature($method->name, $method->parameters);
        }

        $line .= ';';

        return $line;
    }
}
