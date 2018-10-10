<?php

use Faker\Generator as Faker;

$factory->define(App\Item::class, function (Faker $faker) {

    return [
        'description' => $faker->text,
        'size' => $faker->text,
        'price' => $faker->randomFloat
    ];

});
