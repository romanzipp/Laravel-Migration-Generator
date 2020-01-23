<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Conductors\MigrationGeneratorConductor;

class MigrationGeneratorConductorTest extends TestCase
{
    public function testNameBasic()
    {
        $conductor = new MigrationGeneratorConductor('foo', []);
        $this->assertEquals('CreateFooTable', $conductor->getClassName());
    }

    public function testNameUnderscore()
    {
        $conductor = new MigrationGeneratorConductor('foo_bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }

    public function testNameMultipleUnderscore()
    {
        $conductor = new MigrationGeneratorConductor('foo__bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }

    public function testFileNameBasic()
    {
        $conductor = new MigrationGeneratorConductor('foo', []);
        $this->assertRegExp('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo_table/', $conductor->getFileName());
    }

    public function testFileNameUnderscore()
    {
        $conductor = new MigrationGeneratorConductor('foo_bar', []);
        $this->assertRegExp('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo_bar_table/', $conductor->getFileName());
    }

    public function testFileNameMultipleUnderscore()
    {
        $conductor = new MigrationGeneratorConductor('foo__bar', []);
        $this->assertRegExp('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo__bar_table/', $conductor->getFileName());
    }

    public function testBuildDownBodyBasic()
    {
        $this->assertEquals(
            '        Schema::dropIfExists(\'foo\');',
            (new MigrationGeneratorConductor('foo', []))->buildDownBody()
        );
    }

    public function testBuildDownBodyUnderscore()
    {
        $this->assertEquals(
            '        Schema::dropIfExists(\'foo_bar\');',
            (new MigrationGeneratorConductor('foo_bar', []))->buildDownBody()
        );
    }

    public function testBuildDownBodyMultipleUnderscore()
    {
        $this->assertEquals(
            '        Schema::dropIfExists(\'foo__bar\');',
            (new MigrationGeneratorConductor('foo__bar', []))->buildDownBody()
        );
    }
}
