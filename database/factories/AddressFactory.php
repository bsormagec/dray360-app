<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'address_line_1' => $faker->address,
        'address_line_2' => $faker->secondaryAddress,
        'city' => $faker->city,
        'county' => $faker->name,
        'state' => $faker->state,
        'postal_code' => $faker->postcode,
        'country' => $faker->country,
        'hours_of_operation' => $faker->sentence,
        'location_name' => $faker->city,
        'location_phone' => $faker->phoneNumber,
    ];
});
