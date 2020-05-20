<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Address
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection canonicalAddressMatches
 * @property \Illuminate\Database\Eloquent\Collection canonicalAddresses
 * @property \Illuminate\Database\Eloquent\Collection companies
 * @property \Illuminate\Database\Eloquent\Collection companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection contacts
 * @property \Illuminate\Database\Eloquent\Collection orderAddressEvents
 * @property number latitude
 * @property number longitude
 * @property string address_line_1
 * @property string address_line_2
 * @property string city
 * @property string county
 * @property string state
 * @property string postal_code
 * @property string country
 * @property string hours_of_operation
 * @property string location_name
 */
class Address extends Model
{
    use SoftDeletes;

    public $table = 't_addresses';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'latitude',
        'longitude',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'state',
        'postal_code',
        'country',
        'hours_of_operation',
        'location_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'address_line_1' => 'string',
        'address_line_2' => 'string',
        'city' => 'string',
        'county' => 'string',
        'state' => 'string',
        'postal_code' => 'string',
        'country' => 'string',
        'hours_of_operation' => 'string',
        'location_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function canonicalAddressMatches()
    {
        return $this->hasMany(\App\Models\CanonicalAddressMatch::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function canonicalAddresses()
    {
        return $this->hasMany(\App\Models\CanonicalAddress::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 't_address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function orderAddressEvents()
    {
        return $this->hasMany(\App\Models\OrderAddressEvent::class, 't_address_id');
    }
}
