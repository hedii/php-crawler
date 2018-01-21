<?php

use App\Email;
use App\Search;
use Faker\Generator as Faker;

$factory->define(Email::class, function (Faker $faker) {
    return [
        'name' => $faker->safeEmail,
        'search_id' => function () {
            return factory(Search::class)->create()->id;
        }
    ];
});
