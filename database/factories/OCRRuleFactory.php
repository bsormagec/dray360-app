<?php

use App\Models\OCRRule;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(OCRRule::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
        'description' => $faker->sentence,
        'code' => Arr::random([
<<<PYTHONCODE
possible_output_fields = {"shipment_designation":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"import"}
    updated_fields["shipment_designation"] = "import"
PYTHONCODE,
<<<PYTHONCODE
possible_output_fields = {"mbol":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"export"}
    updated_fields["bill_of_lading"] = input_fields["mbol"]
PYTHONCODE,
<<<PYTHONCODE
possible_output_fields = {"port_ramp_of_origin_address_raw_text":{"always":True}}
def runrule(input_fields, updated_fields):
    updated_fields["port_ramp_of_origin_address_raw_text"] = None
PYTHONCODE
        ]),
    ];
});
