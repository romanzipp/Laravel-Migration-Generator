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

        if ($this->column->getNotnull() === false) {
            $methods[] = new MigrationColumnMethod('nullable');
        }

        if ($this->column->getComment() !== null) {
            $methods[] = new MigrationColumnMethod('comment', [$this->column->getComment()]);
        }

        if ($this->column->getDefault() !== null) {
            $methods[] = new MigrationColumnMethod('default', [$this->column->getDefault()]);
        }

        if ($this->column->getUnsigned() === true) {
            $methods[] = new MigrationColumnMethod('unsigned');
        }

        $platformOptions = $this->column->getPlatformOptions();

        if (empty($platformOptions) || ! is_array($platformOptions)) {
            return $methods;
        }

        if (config('migration-generator.append_charset') && array_key_exists('charset', $platformOptions)) {
            $methods[] = new MigrationColumnMethod('charset', [$platformOptions['charset']]);
        }

        if (config('migration-generator.append_collation') && array_key_exists('collation', $platformOptions)) {
            $methods[] = new MigrationColumnMethod('collation', [$platformOptions['collation']]);
        }

        return $methods;
    }

    public function getMethod(): ?MigrationColumnMethod
    {
        switch (get_class($this->column->getType())) {

            case ArrayType::class:
                break;

            case BigIntType::class:
                return new MigrationColumnMethod('bigInteger', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case BinaryType::class:
            case BlobType::class:
                return new MigrationColumnMethod('binary', [$this->column->getName()]);

            case BooleanType::class:
                return new MigrationColumnMethod('boolean', [$this->column->getName()]);

            case DateImmutableType::class:
                break;

            case DateIntervalType::class:
                break;

            case DateTimeImmutableType::class:
                break;

            case DateTimeType::class:
                return new MigrationColumnMethod('dateTime', [$this->column->getName()]);

            case DateTimeTzImmutableType::class:
                break;

            case DateTimeTzType::class:
                return new MigrationColumnMethod('dateTimeTz', [$this->column->getName()]);

            case DateType::class:
                return new MigrationColumnMethod('date', [$this->column->getName()]);

            case DecimalType::class:
                return new MigrationColumnMethod('decimal', [$this->column->getName(), $this->column->getPrecision()]);

            case FloatType::class:
            case GuidType::class:

            case IntegerType::class:
                return new MigrationColumnMethod('integer', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case JsonType::class:
                return new MigrationColumnMethod('json', [$this->column->getName()]);

            case ObjectType::class:
                break;

            case SimpleArrayType::class:
                break;

            case SmallIntType::class:
                return new MigrationColumnMethod('smallInteger', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case StringType::class:
                return new MigrationColumnMethod('string', [$this->column->getName()]);

            case TextType::class:
                return new MigrationColumnMethod('text', [$this->column->getName()]);

            case TimeImmutableType::class:
                break;

            case TimeType::class:
                return new MigrationColumnMethod('time', [$this->column->getName()]);

            case VarDateTimeImmutableType::class:
                break;

            case VarDateTimeType::class:
                break;
        }

        return null;
    }

    public function buildMethodSignature(string $name, array $parameters)
    {
        $method = '->';
        $method .= $name;
        $method .= '(';

        foreach ($parameters as $index => $parameter) {

            if (is_bool($parameter)) {
                $method .= $parameter ? 'true' : 'false';
            } elseif (is_string($parameter)) {
                $method .= sprintf('\'%s\'', $parameter);
            } else {
                $method .= $parameter;
            }

            if ($index + 1 < count($parameters)) {
                $method .= ', ';
            }
        }

        $method .= ')';

        return $method;
    }

    public function __invoke()
    {
        $line = '$table';

        foreach ($this->getChainedMethods() as $method) {

            if ($method == null) {
                continue;
            }

            $line .= $this->buildMethodSignature($method->name, $method->parameters);
        }

        $line .= ';';

        return $line;
    }
}
