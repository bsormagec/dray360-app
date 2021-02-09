<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DictionaryItem extends Model
{
    use SoftDeletes;

    const TEMPLATE_TYPE = 'template',
        ITGCONTAINER_TYPE = 'itgcontainer',
        CARRIER_TYPE = 'carrier';

    public $table = 't_dictionary_items';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_company_id',
        't_tms_provider_id',
        't_user_id',
        'item_type',
        'item_key',
        'item_display_name',
        'item_value',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'item_value' => 'json',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 't_company_id');
    }

    public function tmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 't_tms_provider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 't_user_id');
    }

    public function scopeTemplates($query)
    {
        return $query->where('item_type', self::TEMPLATE_TYPE);
    }
}
