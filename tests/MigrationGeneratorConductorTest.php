<?php

namespace romanzipp\MigrationGenerator\Tests;

use romanzipp\MigrationGenerator\Services\Objects\PendingMigration;

class MigrationGeneratorConductorTest extends TestCase
{
    public function testNameBasic()
    {
        $conductor = new PendingMigration('foo', []);
        $this->assertEquals('CreateFooTable', $conductor->getClassName());
    }

    public function testNameUnderscore()
    {
        $conductor = new PendingMigration('foo_bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }

    public function testNameMultipleUnderscore()
    {
        $conductor = new PendingMigration('foo__bar', []);
        $this->assertEquals('CreateFooBarTable', $conductor->getClassName());
    }

    public function testFileNameBasic()
    {
        $conductor = new PendingMigration('foo', []);
        $this->assertMatchesRegularExpression('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo_table/', $conductor->getFileName());
    }

    public function testFileNameUnderscore()
    {
        $conductor = new PendingMigration('foo_bar', []);
        $this->assertMatchesRegularExpression('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo_bar_table/', $conductor->getFileName());
    }

    public function testFileNameMultipleUnderscore()
    {
        $conductor = new PendingMigration('foo__bar', []);
        $this->assertMatchesRegularExpression('/[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_create_foo__bar_table/', $conductor->getFileName());
    }

    public function testBuildDownBodyBasic()
    {
        $this->assertEquals(
            'Schema::dropIfExists(\'foo\');',
            trim((new PendingMigration('foo', []))->buildDownBody())
        );
    }

    public function testBuildDownBodyUnderscore()
    {
        $this->assertEquals(
            'Schema::dropIfExists(\'foo_bar\');',
            trim((new PendingMigration('foo_bar', []))->buildDownBody())
        );
    }

    public function testBuildDownBodyMultipleUnderscore()
    {
        $this->assertEquals(
            'Schema::dropIfExists(\'foo__bar\');',
            trim((new PendingMigration('foo__bar', []))->buildDownBody())
        );
    }
}
