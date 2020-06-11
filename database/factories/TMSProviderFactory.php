<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TMSProvider;
use Faker\Generator as Faker;

$factory->define(TMSProvider::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
    ];
});
