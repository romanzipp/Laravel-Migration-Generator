<?php

namespace romanzipp\MigrationGenerator\Console\Commands;

use Illuminate\Console\Command;
use romanzipp\MigrationGenerator\Services\MigrationGeneratorService;

class GenerateMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mg:generate
                            {--connection= : The connection to be used}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migrations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var MigrationGeneratorService $service */
        $service = app(MigrationGeneratorService::class);
        $service->command($this);

        if ($connection = $this->option('connection')) {
            $service->connection($connection);
        }

        $service();
    }
}
