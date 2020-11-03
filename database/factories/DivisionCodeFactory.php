<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\DivisionCode;
use Faker\Generator as Faker;

$factory->define(DivisionCode::class, function (Faker $faker) {
    $divisionName = $faker->name;


    return [
        't_company_id' => factory(Company::class),
        't_tms_provider_id' => factory(TMSProvider::class),
        'division_name' => $divisionName,
        'division_code' => $faker->randomNumber()
    ];
});
