<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class MigrateDate extends Command
{
    protected $signature = 'migrate:date {date}';

    protected $description = 'Run only migrations from a specific date (example: 20XX_XX_XX)';

    public function handle()
    {
        $date = $this->argument('date');

        if (!preg_match('/^\d{4}_\d{2}_\d{2}$/', $date)) {
            $this->error('Invalid format. Use: YYYY_MM_DD (example: 20XX_XX_XX)');
            return 1;
        }

        $files = new Filesystem();
        $path = database_path('migrations');
        $migrations = $files->glob($path . '/' . $date . '_*.php');

        if (empty($migrations)) {
            $this->warn("No migrations found for this date: $date");
            return 0;
        }

        $this->info("Running " . count($migrations) . " migrations for: $date\n");

        foreach ($migrations as $migrationFile) {
            $filename = basename($migrationFile);
            $this->call('migrate', [
                '--path' => 'database/migrations/' . $filename,
            ]);
        }

        $this->info("\nMigrations on $date completed successfully.");
        return 0;
    }
}
