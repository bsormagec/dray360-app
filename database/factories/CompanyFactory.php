<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\Address;
use App\Models\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        't_address_id' => factory(Address::class),
        'email_intake_address' => $faker->email,
        'email_intake_address_alt' => $faker->email,
        'default_tms_provider_id' => null,
        'refs_custom_mapping' => ["Peter" => 35, "Ben" => 37, "Joe" => 43],
        'automatic_address_verification_threshold' => $faker->randomDigit,
        't_domain_id' => factory(Domain::class),
    ];
});
