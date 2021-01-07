<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Models\DictionaryItem;

$factory->define(DictionaryItem::class, function (Faker $faker) {
    return [
        't_company_id' => null,
        't_tms_provider_id' => null,
        't_user_id' => null,
        'item_type' => DictionaryItem::TEMPLATE_TYPE,
        'item_key' => Str::random(),
        'item_display_name' => implode(' ', $faker->words(2)),
        'item_value' => null,
    ];
});
