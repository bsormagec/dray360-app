<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\Address;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'request_id' => null, // We should not create ocr requests, since it's handled by the trigger inside the t_job_state_changes table
        'shipment_designation' => Arr::random(['Export', 'Import', 'Domestic']),
        'equipment_type' => Arr::random(['Container', 'Trailer']),
        'shipment_direction' => Arr::random(['Export', 'Import', 'Domestic']),
        'one_way' => $faker->boolean,
        'yard_pre_pull' => $faker->boolean,
        'has_chassis' => $faker->boolean,
        'unit_number' => Arr::random(['ACMU8009943', 'HJCU8281988', 'CSQU3054383', 'TOLU4734787', 'LSCU1077379', 'MSKU2666542', 'NYKU3086856', 'BICU1234565']),
        'equipment_size' => Arr::random(['20 ft', '40 ft', '45 ft', '48ft']),
        'hazardous' => null, // this should roll up from the line item?
        'reference_number' => $faker->lexify(),
        'rate_quote_number' => $faker->numerify('##########'),
        'seal_number' => $faker->bothify('?#######'),
        'vessel' => $faker->firstNameFemale(),
        'voyage' => $faker->numerify('###').strtoupper($faker->lexify('???')),
        'master_bol_mawb' => Arr::random([null, strtoupper($faker->bothify('??????#####????#'))]),
        'house_bol_hawb' => Arr::random([null, strtoupper($faker->bothify('??????#####????#'))]),
        'estimated_arrival_utc' => now()->addDays($faker->numberBetween(1, 10))->toDateTimeString(),
        'last_free_date_utc' => now()->addDays($faker->numberBetween(1, 10))->toDateTimeString(),
        'booking_number' => null,
        'bill_of_lading' => null,
        'bill_to_address_id' => $faker->boolean ? factory(Address::class) : null,
        'port_ramp_of_origin_address_id' => $faker->boolean ? factory(Address::class) : null,
        'port_ramp_of_destination_address_id' => $faker->boolean ? factory(Address::class) : null,
        'ocr_data' => json_encode([$faker->word]),
        'pickup_number' => null,
        'pickup_by_date' => null,
        'pickup_by_time' => null,
        'bill_to_address_verified' => $faker->boolean,
        'bill_to_address_raw_text' => $faker->address,
        'port_ramp_of_origin_address_verified' => $faker->boolean,
        'port_ramp_of_origin_address_raw_text' => $faker->address,
        'port_ramp_of_destination_address_verified' => $faker->boolean,
        'port_ramp_of_destination_address_raw_text' => $faker->address,
        'variant_id' => null,
        'variant_name' => null,
        't_tms_provider_id' => null,
        'tms_shipment_id' => null,
        'carrier' => $faker->company,
        'bill_charge' => $faker->randomFloat(2),
        'bill_comment' => null,
        'line_haul' => $faker->randomFloat(2),
        'rate_box' => null,
        'fuel_surcharge' => $faker->randomFloat(2),
        'total_accessorial_charges' => $faker->randomFloat(2),
        'equipment_provider' => null,
        'actual_destination' => null,
        'actual_origin' => null,
        'customer_number' => null,
        'expedite' => $faker->boolean,
        'load_number' => null,
        'purchase_order_number' => null,
        'release_number' => null,
        'ship_comment' => null,
        'division_code' => '2202',
        't_company_id' => null,
    ];
});
