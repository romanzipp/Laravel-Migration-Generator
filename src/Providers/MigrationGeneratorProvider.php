<?php

namespace romanzipp\MigrationGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use romanzipp\MigrationGenerator\Console\Commands\GenerateMigrationsCommand;

class MigrationGeneratorProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/../config/migration-generator.php' => config_path('migration-generator.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/../config/migration-generator.php',
            'migration-generator'
        );

        $this->commands([
            GenerateMigrationsCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
