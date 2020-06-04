<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Address $address
 * @property \App\Models\Company $company
 * @property \App\Models\TMSProvider $tmsProvider
 * @property integer $t_address_id
 * @property integer $t_company_id
 * @property integer $t_tms_provider_id
 * @property string $company_address_tms_code
 */
class CompanyAddressTMSCode extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';

    public $table = 't_company_address_tms_code';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_address_id',
        't_company_id',
        't_tms_provider_id',
        'company_address_tms_code'
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        't_company_id' => 'integer',
        't_tms_provider_id' => 'integer',
        'company_address_tms_code' => 'string'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        't_address_id' => 'required',
        't_company_id' => 'required',
        't_tms_provider_id' => 'required'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 't_address_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 't_company_id');
    }

    public function tmsProvider()
    {
        return $this->belongsTo(TMSProvider::class, 't_tms_provider_id');
    }

    public function scopeForCompanyTmsProvider(Builder $query, int $companyId, int $tmsProviderId)
    {
        return $query->where([
            't_company_id' => $companyId,
            't_tms_provider_id' => $tmsProviderId,
        ]);
    }

    public static function createFrom($addressCode, $address, $company, $tmsProvider): self
    {
        $companyAddressTmsCode = new static();
        $companyAddressTmsCode->company_address_tms_code = $addressCode;
        $companyAddressTmsCode->address()->associate($address);
        $companyAddressTmsCode->company()->associate($company);
        $companyAddressTmsCode->tmsProvider()->associate($tmsProvider);

        return tap($companyAddressTmsCode)->save();
    }
}
