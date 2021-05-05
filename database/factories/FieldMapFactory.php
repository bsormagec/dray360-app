<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FieldMap;
use Faker\Generator as Faker;

$factory->define(FieldMap::class, function (Faker $faker) {
    return [
        'system_default' => false,
        'fieldmap_config' => '{"unit_number" : {"d3canon_name": "unit_number","d3canon_table": "t_orders","d3canon_column": "unit_number","abbyy_source_field": "equip_info.unit_number","abbyy_source_regex": null,"available": true,"templateable": false,"adminreview_if_missing": false,"adminreview_validation_regex": null,"screen_hide": false,"screen_name": "Unit number","use_template_value": true,"constant_value": null,"post_process_source_field": null,"post_process_source_regex": null,"profittools_destination": "Equipment.eq_ref","cargowise_destination": null,"compcare_destination": null,"notes": null}}',
        'created_at' => now(),
        'replaced_at' => null,
        'replacedby_id' => null,
        'replaces_id' => null,
    ];
});
