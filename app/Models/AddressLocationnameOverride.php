<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
This table is used to improve address matching for address searches.
In particular, this addresses the case where customer "location name" as
defined in their Profit Tools database is an extreme abbreviation of what
would be used by shipment-buyers for location name in their pre-notes.
How it is used is this: when location_name in the profit-tools address
list matches the location_name in this table, then that location_name is
replaced by override_name prior to any
*/


class AddressLocationnameOverride extends Model
{
    use SoftDeletes;

    public $table = 't_address_locationname_overrides';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'location_name',
        'override_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'location_name' => 'required',
        'override_name' => 'required'
    ];
}
