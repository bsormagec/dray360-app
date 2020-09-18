<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Arr;
use App\Models\EquipmentType;
use Faker\Generator as Faker;

$factory->define(EquipmentType::class, function (Faker $faker) {
    $rowType = Arr::random(['combined', 'separate']);

    return [
        't_company_id' => factory(Company::class),
        't_tms_provider_id' => factory(TMSProvider::class),
        'tms_equipment_id' => $faker->word,
        'equipment_owner' => $faker->company,
        'row_type' => $rowType,
        'equipment_type_and_size' => $rowType == 'combined' ? implode(',', $faker->words(2)) : null,
        'equipment_type' => $rowType == 'separate' ? $faker->word : null,
        'equipment_size' => $rowType == 'separate' ? $faker->word : null,
    ];
});
