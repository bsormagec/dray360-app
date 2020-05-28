<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OCRVariant extends Model
{
    use SoftDeletes;

    public $table = 't_ocrvariants';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];



    public $fillable = [
        'abbyy_variant_name',
        'abbyy_variant_id',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'abbyy_variant_id' => 'string',
        'abbyy_variant_name' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'abbyy_variant_id' => 'required',
        'abbyy_variant_name' => 'required',
        'description' => 'required'
    ];
}
