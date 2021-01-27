<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OCRVariant;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;

$factory->define(OCRVariant::class, function (Faker $faker) {
    return [
        'abbyy_variant_id' => $faker->randomNumber(),
        'abbyy_variant_name' => $faker->company,
        'description' => $faker->sentence,
        'variant_type' => Arr::random(['edi', 'tabular', 'ocr']),
        'classification' => [],
        'mapping' => [],
        'company_id_list' => [],
        'admin_review_company_id_list' => [],
    ];
});
