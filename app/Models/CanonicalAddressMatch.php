<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CanonicalAddressMatch
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \App\Models\CanonicalAddress canonicalAddress
 * @property integer t_address_id
 * @property integer t_canonical_address_id
 */
class CanonicalAddressMatch extends Model
{
    use SoftDeletes;

    public $table = 't_canonical_address_matches';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        't_address_id',
        't_canonical_address_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_address_id' => 'integer',
        't_canonical_address_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_address_id' => 'required',
        't_canonical_address_id' => 'required'
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
    public function canonicalAddress()
    {
        return $this->belongsTo(\App\Models\CanonicalAddress::class, 't_canonical_address_id');
    }
}
