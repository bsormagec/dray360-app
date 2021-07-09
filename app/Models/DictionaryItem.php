<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DictionaryItem extends Model
{
    use SoftDeletes;

    const TEMPLATE_TYPE = 'template',
        ITGCONTAINER_TYPE = 'itgcontainer',
        VESSEL_TYPE = 'vessel',
        CARRIER_TYPE = 'carrier',
        PT_IMAGETYPE_TYPE = 'pt-imagetype',
        CC_LOADTYPE_TYPE = 'cc-loadtype'
        ;

    const TYPES_LIST = [
        self::TEMPLATE_TYPE,
        self::ITGCONTAINER_TYPE,
        self::VESSEL_TYPE,
        self::CARRIER_TYPE,
        self::PT_IMAGETYPE_TYPE,
        self::CC_LOADTYPE_TYPE,
    ];

    const TYPES_LIST_OPTIONS = [
        self::TEMPLATE_TYPE => 'Template',
        self::ITGCONTAINER_TYPE => 'ITG Container',
        self::CARRIER_TYPE => 'Carrier',
        self::VESSEL_TYPE => 'Vessel',
        self::PT_IMAGETYPE_TYPE => 'PT Image Type',
        self::CC_LOADTYPE_TYPE => 'CC Load Type',
    ];

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
