<?php

namespace App\Jobs;

use App\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Symfony\Component\Process\Process;

class CrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The search model instance.
     *
     * @var \App\Search
     */
    public $search;

    /**
     * Create a new job instance.
     *
     * @param \App\Search $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! App::environment('testing')) {
            $basePath = base_path();
            $process = new Process("cd {$basePath} && php artisan crawler:crawl {$this->search->id} > /dev/null 2>&1 &");
            $process->start();

            $pid = $process->getPid();

            $this->search->update(['pid' => $pid]);
        }
    }
}
