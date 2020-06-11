<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Faker\Generator as Faker;
use App\Models\CompanyAddressTMSCode;

$factory->define(CompanyAddressTMSCode::class, function (Faker $faker) {
    return [
        't_address_id' => factory(Address::class),
        't_tms_provider_id' => factory(TMSProvider::class),
        't_company_id' => factory(Company::class),
        'company_address_tms_code' => $faker->randomNumber(),
    ];
});
