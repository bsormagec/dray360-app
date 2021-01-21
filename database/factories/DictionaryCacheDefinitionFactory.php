<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\DictionaryItem;
use App\Models\DictionaryCacheDefinition;

$factory->define(DictionaryCacheDefinition::class, function (Faker $faker) {
    return [
        'cache_type' => DictionaryItem::TEMPLATE_TYPE,
        'use_variant_name' => $faker->boolean,
        'use_bill_to_address_raw_text' => $faker->boolean,
        'use_event1_address_raw_text' => $faker->boolean,
        'use_event2_address_raw_text' => $faker->boolean,
    ];
});
