<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Domain;
use App\Models\Tenant;
use Faker\Generator as Faker;

$factory->define(Domain::class, function (Faker $faker) {
    return [
        'description' => $faker->name,
        'hostname' => $faker->domainName,
        't_tenant_id' => factory(Tenant::class),
    ];
});
