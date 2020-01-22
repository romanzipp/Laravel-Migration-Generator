<?php

namespace romanzipp\MigrationGenerator\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\MigrationServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->artisan('migrate')->run();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MigrationServiceProvider::class,
        ];
    }

    /**
     * Get the database connection.
     *
     * @return mixed
     */
    protected function db(): Connection
    {
        return $this->app['db']->connection();
    }

    /**
     * Create test database tables.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('basic_table', function (Blueprint $table) {

            $table->integer('integer');
            $table->unsignedInteger('unsigned_integer');

            $table->string('string');
            $table->string('nullable_string');
            $table->string('string_length_20', 20);

            $table->decimal('decimal', 6,4);
        });
    }
}
