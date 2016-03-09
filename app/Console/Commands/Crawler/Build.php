<?php

namespace App\Console\Commands\Crawler;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the crawler app';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        // check if .env file exists
        if (!file_exists(base_path('.env'))) {
            $this->error('Cannot find the .env file.');

            return false;
        }

        // generate app key
        $this->call('key:generate');

        // create migration table if it doesn't exist
        if (!Schema::hasTable('migrations')) {
            $this->call('migrate:install');
        }

        // migrate the database
        $this->call('migrate');

        // send success message
        $this->info('Crawler app ready!');

        return true;
    }
}
