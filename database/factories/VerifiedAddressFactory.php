<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use App\Models\TMSProvider;
use Faker\Generator as Faker;
use App\Models\VerifiedAddress;

$factory->define(VerifiedAddress::class, function (Faker $faker) {
    return [
        't_company_id' => factory(Company::class),
        't_tms_provider_id' => factory(TMSProvider::class),
        'ocr_address_raw_text' => $faker->address,
        'company_address_tms_code' => $faker->randomDigitNotNull,
        'company_address_tms_text' => $faker->address,
        'verified_count' => $faker->randomDigitNotNull,
        'skip_verification' => $faker->boolean,
        'deleted_reason' => $faker->paragraph,
    ];
});
