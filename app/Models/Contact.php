<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \App\Models\Company company
 * @property \Illuminate\Database\Eloquent\Collection canonicalAddresses
 * @property integer t_company_id
 * @property integer t_address_id
 * @property string title
 * @property string first_name
 * @property string last_name
 * @property string phone1_number
 * @property string phone1_ext
 * @property string phone1_number_type
 * @property string phone2_number
 * @property string phone2_ext
 * @property string phone2_number_type
 * @property string phone3_number
 * @property string phone3_ext
 * @property string phone3_number_type
 * @property string email
 */
class Contact extends Model
{
    use SoftDeletes;

    public $table = 't_contacts';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        't_company_id',
        't_address_id',
        'title',
        'first_name',
        'last_name',
        'phone1_number',
        'phone1_ext',
        'phone1_number_type',
        'phone2_number',
        'phone2_ext',
        'phone2_number_type',
        'phone3_number',
        'phone3_ext',
        'phone3_number_type',
        'email'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_company_id' => 'integer',
        't_address_id' => 'integer',
        'title' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'phone1_number' => 'string',
        'phone1_ext' => 'string',
        'phone1_number_type' => 'string',
        'phone2_number' => 'string',
        'phone2_ext' => 'string',
        'phone2_number_type' => 'string',
        'phone3_number' => 'string',
        'phone3_ext' => 'string',
        'phone3_number_type' => 'string',
        'email' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_company_id' => 'required',
        't_address_id' => 'required'
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function canonicalAddresses()
    {
        return $this->hasMany(\App\Models\CanonicalAddress::class, 't_contact_id');
    }
}
