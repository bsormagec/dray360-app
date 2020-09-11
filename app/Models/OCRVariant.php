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
    protected $casts = [];

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

    public function companiesAccesorials()
    {
        return $this->belongsToMany(
            Company::class,
            't_company_ocrvariant_accessorial_mappings',
            't_ocrvariant_id',
            't_company_id'
        )->using(AccesorialMappingPivot::class);
    }
}
