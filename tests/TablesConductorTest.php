<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Conductors\TablesConductor;

class TablesConductorTest extends TestCase
{
    public function testGetTables()
    {
        $tables = (new TablesConductor($this->db()))->getTables();

        $this->assertTrue(in_array('unsigned_integers', $tables));
        $this->assertTrue(in_array('complete_table_nullable', $tables));
        $this->assertTrue(in_array('complete_table', $tables));
    }

    public function testExcludedTables()
    {
        $tables = (new TablesConductor($this->db()))->getTables();

        $this->assertFalse(in_array('migrations', $tables));
        $this->assertFalse(in_array('sqlite_master', $tables));
        $this->assertFalse(in_array('sqlite_sequence', $tables));
        $this->assertFalse(in_array('sqlite_stat1', $tables));
    }
}
