<?php

namespace romanzipp\MigrationGenerator\Services\Objects;

use Illuminate\Support\Str;
use romanzipp\MigrationGenerator\Services\Conductors\ColumnInfoConductor;

class PendingMigration
{
    const STUB = __DIR__ . '/../../../stubs/migration.stub';

    const IND4 = '    ';
    const IND8 = '        ';
    const IND12 = '            ';

    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $stub;

    /**
     * @var string
     */
    private $fileName;

    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->stub = $this->generateStub();
        $this->fileName = $this->generateFileName();
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getStub(): string
    {
        return $this->stub;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Generate the migration class name.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return 'Create' . ucfirst(Str::camel($this->table)) . 'Table';
    }

    /**
     * Generate the "up" database migration method.
     *
     * @return string
     */
    public function buildUpBody(): string
    {
        $lines = [];

        $lines[] = self::IND8 . sprintf('Schema::create(\'%s\', function (Blueprint $table) {', $this->table);

        foreach ($this->columns as $column) {

            $info = new ColumnInfoConductor($column);
            $lines[] = self::IND12 . $info();
        }

        $lines[] = self::IND8 . '});';

        return implode(PHP_EOL, $lines);
    }

    /**
     * Generate the "down" database migration method.
     *
     * @return string
     */
    public function buildDownBody(): string
    {
        return self::IND8 . sprintf('Schema::dropIfExists(\'%s\');', $this->table);
    }

    /**
     * Generate the stub
     *
     * @return string
     */
    private function generateStub(): string
    {
        $stub = file_get_contents(self::STUB);

        $stub = str_replace('{TABLE}', $this->getClassName(), $stub);
        $stub = str_replace('{UP_BODY}', $this->buildUpBody(), $stub);
        $stub = str_replace('{DOWN_BODY}', $this->buildDownBody(), $stub);

        return $stub;
    }

    /**
     * Generate the migration file name.
     *
     * @return string
     */
    private function generateFileName(): string
    {
        $fileName = config('migration-generator.file_name_template');

        $fileName = str_replace('{date}', date('Y_m_d_His'), $fileName);
        $fileName = str_replace('{table}', $this->table, $fileName);

        return $fileName;
    }
}
