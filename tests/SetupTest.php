<?php

namespace romanzipp\MigrationGenerator\Tests;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;

class SetupTest extends TestCase
{
    public function testTableCreation()
    {
        $info = $this->db()->getSchemaBuilder()->getColumnListing('basic_table_incrementing_timestamps');

        $this->assertEquals([
            'id',
            'created_at',
            'updated_at',
        ], $info);

        $this->assertInstanceOf(IntegerType::class, $this->db()->getDoctrineColumn('basic_table_incrementing_timestamps', 'id')->getType());
        $this->assertInstanceOf(DateTimeType::class, $this->db()->getDoctrineColumn('basic_table_incrementing_timestamps', 'created_at')->getType());
    }
}
