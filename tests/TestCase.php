<?php

namespace romanzipp\MigrationGenerator\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use romanzipp\MigrationGenerator\Providers\MigrationGeneratorProvider;

abstract class TestCase extends BaseTestCase
{
    const OUTPUT_DIR = __DIR__ . '/Support/files';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        config(['migration-generator.path' => __DIR__ . '/Support/files']);

        $this->artisan('migrate:fresh')->run();

        $this->setUpDatabase($this->app);
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
            MigrationGeneratorProvider::class,
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
        $app['db']->connection()->getSchemaBuilder()->create('auto_increment_integer', function (Blueprint $table) {
            $table->integer('integer')->autoIncrement();
        });

        $app['db']->connection()->getSchemaBuilder()->create('auto_increment_big_integer', function (Blueprint $table) {
            $table->bigInteger('bigInteger')->autoIncrement();
        });

        $app['db']->connection()->getSchemaBuilder()->create('auto_increment_small_integer', function (Blueprint $table) {
            $table->smallInteger('smallInteger')->autoIncrement();
        });

        $app['db']->connection()->getSchemaBuilder()->create('unsigned_integers', function (Blueprint $table) {
            $table->bigInteger('bigInteger')->unsigned();
            $table->integer('integer')->unsigned();
            $table->smallInteger('smallInteger')->unsigned();
        });

        $app['db']->connection()->getSchemaBuilder()->create('precisions', function (Blueprint $table) {
            $table->decimal('decimal_8_2', 8, 2)->nullable();
            $table->decimal('decimal_10_10', 10, 10)->nullable();
        });

        $app['db']->connection()->getSchemaBuilder()->create('complete_table_nullable', function (Blueprint $table) {
            $table->bigInteger('bigIntegerNullable')->nullable();
            $table->binary('binaryNullable')->nullable();
            $table->boolean('booleanNullable')->nullable();
            $table->dateTime('dateTimeNullable')->nullable();
            $table->dateTimeTz('dateTimeTzNullable')->nullable();
            $table->date('dateNullable')->nullable();
            $table->decimal('decimalNullable')->nullable();
            $table->integer('integerNullable')->nullable();
            $table->json('jsonNullable')->nullable();
            $table->smallInteger('smallIntegerNullable')->nullable();
            $table->string('stringNullable')->nullable();
            $table->text('textNullable')->nullable();
            $table->time('timeNullable')->nullable();
        });

        $app['db']->connection()->getSchemaBuilder()->create('complete_table', function (Blueprint $table) {
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

    protected function isMySQL(): bool
    {
        return $this->db() instanceof MySqlConnection;
    }

    protected function isSQLite(): bool
    {
        return $this->db() instanceof SQLiteConnection;
    }
}
