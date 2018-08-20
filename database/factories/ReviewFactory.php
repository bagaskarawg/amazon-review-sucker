<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Review::class, function (Faker $faker) {
    return [
        'product_id' => $faker->biasedNumberBetween(1, 25),
        'child_asin' => $faker->ean13,
        'title' => $faker->realText(50),
        'body' => $faker->realText(450),
        'review_date' => $faker->dateTimeThisYear(),
        'author' => $faker->name(),
        'author_link' => $faker->url,
        'number_of_comments' => $faker->biasedNumberBetween(0, 25),
        'has_photo' => $faker->boolean(50),
        'has_video' => $faker->boolean(50),
        'verified' => $faker->boolean(50),
    ];
});
