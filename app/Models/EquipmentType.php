<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Company $company
 * @property \App\Models\TMSProvider $tmsProvider
 * @property int t_company_id
 * @property int t_tms_provider_id
 * @property string tms_equipment_id
 * @property string equipment_owner
 * @property string row_type
 * @property string equipment_type_and_size
 * @property string equipment_type
 * @property string equipment_size
 * @property string equipment_display
 * @property string scac
 */
class EquipmentType extends Model
{
    use SoftDeletes;
    use BelongsToCompany;

    public $table = 't_equipment_types';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_company_id',
        't_tms_provider_id',
        'tms_equipment_id',
        'equipment_owner',
        'row_type',
        'equipment_type_and_size',
        'equipment_type',
        'equipment_size',
        'line_prefix_list',
        'scac',
    ];

    protected $casts = [
        'line_prefix_list' => 'array',
    ];

    public function tmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 't_tms_provider_id');
    }

    public function scopeForCompanyAndTmsProvider($query, int $companyId, int $tmsProviderId)
    {
        return $query->where([
            't_company_id' => $companyId,
            't_tms_provider_id' => $tmsProviderId,
        ]);
    }
}
