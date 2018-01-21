<?php

use App\Search;
use App\User;
use Faker\Generator as Faker;

$factory->define(Search::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'is_limited' => $faker->boolean,
        'pid' => null
    ];
});

$factory->state(Search::class, 'created', ['status' => Search::STATUS_CREATED]);
$factory->state(Search::class, 'running', ['status' => Search::STATUS_RUNNING]);
$factory->state(Search::class, 'finished', ['status' => Search::STATUS_FINISHED]);
$factory->state(Search::class, 'paused', ['status' => Search::STATUS_PAUSED]);
$factory->state(Search::class, 'failed', ['status' => Search::STATUS_FAILED]);
$factory->state(Search::class, 'limited', ['is_limited' => true]);
$factory->state(Search::class, 'unlimited', ['is_limited' => false]);
