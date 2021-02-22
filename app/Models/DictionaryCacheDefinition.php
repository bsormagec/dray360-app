<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictionaryCacheDefinition extends Model
{
    public $table = 't_dictionary_cache_definitions';

    public $fillable = [
        'cache_type',
        'use_variant_name',
        'use_bill_to_address_raw_text',
        'use_event1_address_raw_text',
        'use_event2_address_raw_text',
        'use_event3_address_raw_text',
        'use_hazardous',
        'use_equipment_size',
        'use_vessel',
        'use_carrier',
        'use_shipment_direction',
        'use_template_key',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'use_variant_name' => 'boolean',
        'use_bill_to_address_raw_text' => 'boolean',
        'use_event1_address_raw_text' => 'boolean',
        'use_event2_address_raw_text' => 'boolean',
        'use_hazardous' => 'boolean',
        'use_equipment_size' => 'boolean',
        'use_vessel' => 'boolean',
        'use_carrier' => 'boolean',
        'use_shipment_direction' => 'boolean',
        'use_event3_address_raw_text' => 'boolean',
        'use_template_key' => 'boolean',
    ];

    public static function cacheDefinitionForType($type): ?self
    {
        return static::where('cache_type', $type)->firstOrFail();
    }
}
