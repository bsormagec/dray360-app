<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CompanyAddressTMSCode
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \App\Models\Company company
 * @property integer t_address_id
 * @property integer t_company_id
 * @property integer t_tms_provider_id
 * @property string company_address_tms_code
 */
class CompanyAddressTMSCode extends Model
{
    use SoftDeletes;

    public $table = 't_company_address_tms_code';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        't_address_id',
        't_company_id',
        't_tms_provider_id',
        'company_address_tms_code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
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
     *
     * @var array
     */
    public static $rules = [
        't_address_id' => 'required',
        't_company_id' => 'required',
        't_tms_provider_id' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 't_company_id');
    }
}
