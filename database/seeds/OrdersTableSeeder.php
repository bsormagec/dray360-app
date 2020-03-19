<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for ($x = 0; $x <= 52; $x++) {
            DB::table('t_orders')->insert([
                //'request_id' => Str::random(10),
                'shipment_designation' => Str::random(10),
                'equipment_type' => Str::random(10),
                'shipment_direction' => Str::random(10),
                'one_way' => true,
                'yard_pre_pull' => true,
                'has_chassis' => true,
                'unit_number' => Str::random(10),
                'equipment_size' => Str::random(10),
                'owner_or_ss_company' => Str::random(10),
                'hazardous' => false,
                'expedite_shipment' => false,
                'reference_number' => Str::random(10),
                'rate_quote_number' => Str::random(10),
                'port_ramp_of_origon' => Str::random(10),
                'port_ramp_of_destination' => Str::random(10),
                'vessel' => Str::random(10),
                'voyage' => Str::random(10),
                'house_bol_hawb' => Str::random(10),
                'estimated_arrival_utc' => Carbon::now()->format('Y-m-d H:i:s'),
                'last_free_date_utc' => Carbon::now()->format('Y-m-d H:i:s'),

            ]);
        }

    }
}
