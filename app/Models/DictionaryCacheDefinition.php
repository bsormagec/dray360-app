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
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'use_variant_name' => 'boolean',
        'use_bill_to_address_raw_text' => 'boolean',
        'use_event1_address_raw_text' => 'boolean',
        'use_event2_address_raw_text' => 'boolean',
    ];

    public static function template(): ?self
    {
        $definition = static::where('cache_type', DictionaryItem::TEMPLATE_TYPE)->first();

        if ($definition) {
            return $definition;
        }

        return new static([
            'use_variant_name' => false,
            'use_bill_to_address_raw_text' => false,
            'use_event1_address_raw_text' => false,
            'use_event2_address_raw_text' => false,
        ]);
    }
}
