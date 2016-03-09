<?php

namespace App\Providers;

use App\Crawler\Crawler;
use Hedii\Extractors\Extractor;
use Illuminate\Support\ServiceProvider;

class CrawlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Hedii\Crawler', function () {
            return new Crawler(new Extractor());
        });
    }
}
