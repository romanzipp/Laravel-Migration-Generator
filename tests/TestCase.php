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
        $app['db']->connection()->getSchemaBuilder()->create('basic_table_nullable', function (Blueprint $table) {
            $table->bigInteger('bigInteger')->nullable();
            $table->binary('binary')->nullable();
            $table->boolean('boolean')->nullable();
            $table->dateTime('dateTime')->nullable();
            $table->dateTimeTz('dateTimeTz')->nullable();
            $table->date('date')->nullable();
            $table->decimal('decimal')->nullable();
            $table->integer('integer')->nullable();
            $table->json('json')->nullable();
            $table->smallInteger('smallInteger')->nullable();
            $table->string('string')->nullable();
            $table->text('text')->nullable();
            $table->time('time')->nullable();
        });

        $app['db']->connection()->getSchemaBuilder()->create('basic_table', function (Blueprint $table) {
            $table->bigInteger('bigInteger');
            $table->binary('binary');
            $table->boolean('boolean');
            $table->dateTime('dateTime');
            $table->dateTimeTz('dateTimeTz');
            $table->date('date');
            $table->decimal('decimal');
            $table->integer('integer');
            $table->json('json');
            $table->smallInteger('smallInteger');
            $table->string('string');
            $table->text('text');
            $table->time('time');
        });
    }
}
