<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \Illuminate\Database\Eloquent\Collection companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection contacts
 * @property integer t_address_id
 * @property string name
 */
class Company extends Model
{
    use SoftDeletes;

    public $table = 't_companies';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        't_address_id',
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_address_id' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function address()
    {
        return $this->belongsTo(\App\Models\AAddress::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 't_company_id');
    }
}
