<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Illuminate\Support\Arr;
use App\Models\OrderLineItem;
use Faker\Generator as Faker;
use Bezhanov\Faker\Provider\Commerce;

$factory->define(OrderLineItem::class, function (Faker $faker) {
    $faker->addProvider(new Commerce($faker)); // needed for productName

    $quantity = $faker->numberBetween(1, 1000);
    $weight = $faker->numberBetween(1, 100);

    return [
        't_order_id' => factory(Order::class),
        'quantity' => $quantity,
        'unit_of_measure' => Arr::random(['BAG', 'BOX', 'LOT', 'CAS', 'CRT', 'TU', 'ROL', 'PAD', 'PAL', 'FT']),  // https://www.doa.la.gov/osp/Vendorcenter/publications/unitofmeasurecodes.pdf
        'contents' => $faker->productName,
        'weight' => $weight,
        'total_weight' => $quantity * $weight,
        'weight_uom' => Arr::random(['TON', 'LBS', 'KGS']),
        'is_hazardous' => $faker->boolean,
        'haz_contact_name' => null,
        'haz_phone' => null,
        'haz_un_code' => null,
        'haz_un_name' => null,
        'haz_class' => null,
        'haz_qualifier' => null,
        'haz_description' => null,
        'haz_imdg_page_number' => null,
        'haz_flashpoint_temp' => null,
        'haz_flashpoint_temp_uom' => null,
        'packaging_group' => null,
    ];
});
