<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types;
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
     * Get all methods as array for a single column.
     *
     * @return MigrationColumnMethod[]
     */
    public function getChainedMethods(): array
    {
        $methods = [];

        $methods[] = $this->getMethod();

        if (false === $this->column->getNotnull()) {
            $methods[] = new MigrationColumnMethod('nullable');
        }

        if (null !== $this->column->getComment()) {
            $methods[] = new MigrationColumnMethod('comment', [$this->column->getComment()]);
        }

        if (null !== $this->column->getDefault()) {
            if ('CURRENT_TIMESTAMP' === $this->column->getDefault()) {
                $methods[] = new MigrationColumnMethod('useCurrent');
            } else {
                $methods[] = new MigrationColumnMethod('default', [$this->column->getDefault()]);
            }
        }

        if (true === $this->column->getUnsigned()) {
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

    /**
     * Get initial column method.
     *
     * @return MigrationColumnMethod|null
     */
    public function getMethod(): ?MigrationColumnMethod
    {
        switch (get_class($this->column->getType())) {
            case Types\ArrayType::class:
                break;

            case Types\BigIntType::class:
                return new MigrationColumnMethod('bigInteger', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case Types\BinaryType::class:
            case Types\BlobType::class:
                return new MigrationColumnMethod('binary', [$this->column->getName()]);

            case Types\BooleanType::class:
                return new MigrationColumnMethod('boolean', [$this->column->getName()]);

            case Types\DateImmutableType::class:
                break;

            case Types\DateIntervalType::class:
                break;

            case Types\DateTimeImmutableType::class:
                break;

            case Types\DateTimeType::class:
                return new MigrationColumnMethod('dateTime', [$this->column->getName()]);

            case Types\DateTimeTzImmutableType::class:
                break;

            case Types\DateTimeTzType::class:
                return new MigrationColumnMethod('dateTimeTz', [$this->column->getName()]);

            case Types\DateType::class:
                return new MigrationColumnMethod('date', [$this->column->getName()]);

            case Types\DecimalType::class:
                return new MigrationColumnMethod('decimal', [$this->column->getName(), $this->column->getPrecision()]);

            case Types\FloatType::class:
            case Types\GuidType::class:

            case Types\IntegerType::class:
                return new MigrationColumnMethod('integer', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case Types\JsonType::class:
                return new MigrationColumnMethod('json', [$this->column->getName()]);

            case Types\ObjectType::class:
                break;

            case Types\SimpleArrayType::class:
                break;

            case Types\SmallIntType::class:
                return new MigrationColumnMethod('smallInteger', [$this->column->getName(), $this->column->getAutoincrement(), $this->column->getUnsigned()]);

            case Types\StringType::class:

                if (false === $this->column->getFixed()) {
                    return new MigrationColumnMethod('string', [$this->column->getName()]);
                }

                // Char type & length of 36 is most likely UUID
                if (36 === $this->column->getLength()) {
                    return new MigrationColumnMethod('uuid', [$this->column->getName()]);
                }

                return new MigrationColumnMethod('char', [$this->column->getName(), $this->column->getLength()]);

            case Types\TextType::class:
                return new MigrationColumnMethod('text', [$this->column->getName()]);

            case Types\TimeImmutableType::class:
                break;

            case Types\TimeType::class:
                return new MigrationColumnMethod('time', [$this->column->getName()]);

            case Types\VarDateTimeImmutableType::class:
                break;

            case Types\VarDateTimeType::class:
                break;
        }

        return null;
    }

    /**
     * Build the method signature string.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return string
     */
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
            if (null == $method) {
                continue;
            }

            $line .= $this->buildMethodSignature($method->name, $method->parameters);
        }

        $line .= ';';

        return $line;
    }
}
