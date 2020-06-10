<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\Address;
use Faker\Generator as Faker;
use App\Models\OrderAddressEvent;

$factory->define(OrderAddressEvent::class, function (Faker $faker) {
    return [
        'address_schedule_description' => $faker->address,
        't_order_id' => factory(Order::class),
        't_address_id' => factory(Address::class),
        'event_number' => $faker->numberBetween(1, 100), //this has to be set depending on the position in the array
        'is_hook_event' => $faker->boolean,
        'is_mount_event' => $faker->boolean,
        'is_deliver_event' => $faker->boolean,
        'is_dismount_event' => $faker->boolean,
        'is_drop_event' => $faker->boolean,
        'call_for_appointment' => null,
        'delivery_window_from_localtime' => $faker->time(),
        'delivery_window_to_localtime' => $faker->time(),
        'delivery_instructions' => $faker->sentence,
        'unparsed_event_type' => $faker->paragraph,
        't_address_verified' => $faker->boolean,
        't_address_raw_text' => $faker->address,
    ];
});
