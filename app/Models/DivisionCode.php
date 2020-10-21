<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Company $company
 * @property \App\Models\TMSProvider $tmsProvider
 * @property int id
 * @property int t_company_id
 * @property int t_tms_provider_id
 * @property string division_code
 * @property string division_name
 */
class DivisionCode extends Model
{
    use SoftDeletes;
    use BelongsToCompany;

    public $table = 't_division_code';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        't_company_id',
        't_tms_provider_id',
        'division_code',
        'division_name',
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
