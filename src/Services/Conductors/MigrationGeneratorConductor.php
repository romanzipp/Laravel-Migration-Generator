<?php

namespace romanzipp\MigrationGenerator\Services\Conductors;

use Illuminate\Support\Str;

class MigrationGeneratorConductor
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
     * @var \Doctrine\DBAL\Schema\Column[]
     */
    private $columns;

    /**
     * @var string
     */
    private $stub;

    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    public function __invoke()
    {
        $this->stub = file_get_contents(self::STUB);

        $this->replace('{TABLE}', $this->getClassName());
        $this->replace('{UP_BODY}', $this->buildUpBody());
        $this->replace('{DOWN_BODY}', $this->buildDownBody());

        dd($this->columns, $this->stub);

        return $this;
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
     * Generate the migration file name.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return date('Y_m_d_His') . '_create_' . $this->table . '_table.php';
    }

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

    public function buildDownBody(): string
    {
        return self::IND8 . sprintf('Schema::dropIfExists(\'%s\');', $this->table);
    }

    private function replace(string $find, string $replace)
    {
        $this->stub = str_replace($find, $replace, $this->stub);
    }
}
