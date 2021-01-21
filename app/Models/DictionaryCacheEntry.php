<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictionaryCacheEntry extends Model
{
    public $table = 't_dictionary_cache_entries';

    public $fillable = [
        't_dictionary_item_id',
        't_company_id',
        'cache_type',
        'verified_count',
        'cached_variant_name',
        'cached_bill_to_address_raw_text',
        'cached_event1_address_raw_text',
        'cached_event2_address_raw_text',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'verified_count' => 'integer',
    ];

    public function dictionaryItem()
    {
        return $this->belongsTo(DictionaryItem::class, 't_dictionary_item_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 't_company_id');
    }
}
