<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\CompanyDailyMetric;

$factory->define(CompanyDailyMetric::class, function (Faker $faker) {
    return [
        'metric_date' => $faker->date(),
        't_company_id' => null,
    ];
});
