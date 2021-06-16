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
        'contents' => $faker->productName,
        'multiline_contents' => $faker->productName,
        'weight' => $weight,
        'total_weight' => $quantity * $weight,
        'weight_uom' => Arr::random(['TON', 'LBS', 'KGS']),
        'is_hazardous' => $faker->boolean,
    ];
});
