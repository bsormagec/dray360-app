<?php

namespace App\Models;

use App\Contracts\CurrentCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Address $address
 * @property \Illuminate\Database\Eloquent\Collection $companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection $contacts
 * @property integer $t_address_id
 * @property string $name
 */
class Company extends Model implements CurrentCompany
{
    use SoftDeletes;

    const CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at',
        FOREIGN_KEY = 't_company_id',
        CUSHING = 'Cushing',
        TCOMPANIES_DEV = 'TCompaniesDev',
        POLARIS = 'Polaris';

    public $table = 't_companies';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_address_id',
        'name',
        'email_intake_address',
        'email_intake_address_alt',
        'default_tms_provider_id',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        't_address_id' => 'required'
    ];

    public function address()
    {
        return $this->belongsTo(\App\Models\AAddress::class, 't_address_id');
    }

    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_company_id');
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 't_company_id');
    }

    /**
     * Get company 'Cushing'
     */
    public static function getCushing(): self
    {
        return static::where('name', static::CUSHING)->first();
    }

    /**
     * Get company 'TCompanies Dev'
     */
    public static function getTCompaniesDev(): self
    {
        return static::where('name', static::TCOMPANIES_DEV)->first();
    }
}
