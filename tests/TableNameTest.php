<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor;

class TableNameTest extends TestCase
{
    public function testBasicName()
    {
        $conductor = new MigrationGeneratorConductor('foo', []);
        $this->assertEquals('CreateFooTable', $conductor->getClassName());
    }

    public function testUnderscoreName()
    {
        $conductor = new MigrationGeneratorConductor('foo_bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }

    public function testMultipleUnderscoreName()
    {
        $conductor = new MigrationGeneratorConductor('foo__bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }
}
