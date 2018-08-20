<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'asin' => $faker->ean13,
        'state' => App\Models\Product::$states[$faker->biasedNumberBetween(0, 2)],
        'notes' => $faker->realText(),
    ];
});
