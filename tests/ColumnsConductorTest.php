<?php

namespace romanzipp\MigrationGenerator\Tests;

use Doctrine\DBAL\Schema\Column;
use romanzipp\MigrationGenerator\Services\Conductors\ColumnsConductor;

class ColumnsConductorTest extends TestCase
{
    public function testColumnsReturnType()
    {
        $this->assertIsArray(
            (new ColumnsConductor($this->db(), 'complete_table'))->getColumns()
        );
    }

    public function testColumnsHasNumericKeys()
    {
        $this->assertArrayHasKey(
            0,
            (new ColumnsConductor($this->db(), 'complete_table'))->getColumns()
        );
    }

    public function testColumnArrayItemType()
    {
        $this->assertInstanceOf(
            Column::class,
            (new ColumnsConductor($this->db(), 'complete_table'))->getColumns()[0]
        );
    }
}
