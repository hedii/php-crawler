<?php

use App\Search;
use App\Url;
use Faker\Generator as Faker;

$factory->define(Url::class, function (Faker $faker) {
    return [
        'name' => $faker->url,
        'search_id' => function () {
            return factory(Search::class)->create()->id;
        },
        'is_crawled' => $faker->boolean
    ];
});

$factory->state(Url::class, 'crawled', ['is_crawled' => true]);
$factory->state(Url::class, 'not_crawled', ['is_crawled' => false]);