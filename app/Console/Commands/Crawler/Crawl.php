<?php

namespace App\Console\Commands\Crawler;

use App\Search;
use App\Crawler\Crawler;
use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:crawl {search_id : The unique id of the search}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a search crawl';

    /**
     * The search Eloquent model.
     *
     * @var \App\Search
     */
    protected $search;

    /**
     * @var \App\Crawler\Crawler
     */
    protected $crawler;

    /**
     * Create a new command instance.
     *
     * @param \App\Crawler\Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        parent::__construct();

        $this->crawler = $crawler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->search = Search::find($this->argument('search_id'));

        if ($this->search === null) {
            return false;
        }

        return $this->crawler->run($this->search);
    }
}
