<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OCRRule extends Model
{
    use SoftDeletes;

    public $table = 't_ocrrules';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'description',
        'code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'description' => 'required',
        'code' => 'required'
    ];
}
