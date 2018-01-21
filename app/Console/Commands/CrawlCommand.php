<?php

namespace App\Console\Commands;

use App\Search;
use App\Services\Crawler\Crawler;
use Illuminate\Console\Command;

class CrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:crawl {search_id : A search id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a new crawler on a given search';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (! $search = Search::find($this->argument('search_id'))) {
            $this->error("No search with the id `{$this->argument('search_id')}`");

            return false;
        }

        $crawler = new Crawler($search);

        return $crawler->run();
    }
}
