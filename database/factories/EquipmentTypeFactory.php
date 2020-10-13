<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Arr;
use App\Models\EquipmentType;
use Faker\Generator as Faker;

$factory->define(EquipmentType::class, function (Faker $faker) {
    $rowType = Arr::random(['combined', 'separate']);
    $equipmentSize = Arr::random(['20 ft', '40 ft', '45 ft', '48ft']);
    $equipmentType = Arr::random(['CH', 'RF', 'TANK', 'HD', 'ST', 'OT']);

    return [
        't_company_id' => factory(Company::class),
        't_tms_provider_id' => factory(TMSProvider::class),
        'tms_equipment_id' => $faker->word,
        'equipment_owner' => $faker->company,
        'row_type' => $rowType,
        'equipment_type_and_size' => $rowType == 'combined' ? implode(',', [$equipmentType, $equipmentSize]) : null,
        'equipment_type' => $rowType == 'separate' ? $equipmentType : null,
        'equipment_size' => $equipmentSize,
    ];
});
