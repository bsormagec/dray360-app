<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OCRVariant;
use Faker\Generator as Faker;

$factory->define(OCRVariant::class, function (Faker $faker) {
    return [
        'abbyy_variant_id' => $faker->randomNumber(),
        'abbyy_variant_name' => $faker->company,
        'description' => $faker->sentence,
    ];
});
