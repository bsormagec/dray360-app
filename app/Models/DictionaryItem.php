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
        CC_LOADTYPE_TYPE = 'cc-loadtype',
        CC_ORDERSTATUS_TYPE = 'cc-orderstatus',
        CC_HAULCLASS_TYPE = 'cc-haulclass',
        CC_ORDERCLASS_TYPE = 'cc-orderclass',
        CC_LOADEDEMPTY_TYPE = 'cc-loadedempty',
        TERMDIV_TYPE = 'termdiv',
        CC_CONTAINERSIZE_TYPE = 'cc-containersize',
        CC_CONTAINERTYPE_TYPE = 'cc-containertype',
        PT_EQUIPMENTTYPE_TYPE = 'pt-equipmenttype'
        ;

    const TYPES_LIST = [
        self::TEMPLATE_TYPE,
        self::ITGCONTAINER_TYPE,
        self::VESSEL_TYPE,
        self::CARRIER_TYPE,
        self::PT_IMAGETYPE_TYPE,
        self::CC_LOADTYPE_TYPE,
        self::CC_ORDERSTATUS_TYPE,
        self::CC_HAULCLASS_TYPE,
        self::CC_ORDERCLASS_TYPE,
        self::CC_LOADEDEMPTY_TYPE,
        self::TERMDIV_TYPE,
        self::CC_CONTAINERSIZE_TYPE,
        self::CC_CONTAINERTYPE_TYPE,
        self::PT_EQUIPMENTTYPE_TYPE,
    ];

    const TYPES_LIST_OPTIONS = [
        self::TEMPLATE_TYPE => 'Template',
        self::ITGCONTAINER_TYPE => 'ITG Container',
        self::CARRIER_TYPE => 'Carrier',
        self::VESSEL_TYPE => 'Vessel',
        self::PT_IMAGETYPE_TYPE => 'PT Image Type',
        self::PT_EQUIPMENTTYPE_TYPE => 'PT Equipment Type',
        self::CC_LOADTYPE_TYPE => 'CC Load Type',
        self::CC_ORDERSTATUS_TYPE => 'CC Order Status',
        self::CC_HAULCLASS_TYPE => 'CC Haul Class',
        self::CC_ORDERCLASS_TYPE => 'CC Order Class',
        self::CC_LOADEDEMPTY_TYPE => 'CC Loaded Emtty',
        self::TERMDIV_TYPE => 'Terminal/Division',
        self::CC_CONTAINERSIZE_TYPE => 'CC Container Size',
        self::CC_CONTAINERTYPE_TYPE => 'CC Container Type',
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
