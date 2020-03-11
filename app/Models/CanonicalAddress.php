<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CanonicalAddress
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \App\Models\Contact contact
 * @property \Illuminate\Database\Eloquent\Collection canonicalAddressMatches
 * @property integer t_address_id
 * @property integer t_contact_id
 */
class CanonicalAddress extends Model
{
    use SoftDeletes;

    public $table = 't_canonical_addresses';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        't_address_id',
        't_contact_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        't_contact_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_address_id' => 'required',
        't_contact_id' => 'required'
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
    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class, 't_contact_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function canonicalAddressMatches()
    {
        return $this->hasMany(\App\Models\CanonicalAddressMatch::class, 't_canonical_address_id');
    }
}
