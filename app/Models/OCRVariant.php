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
        'description',
        'variant_type',
        'classification',
        'mapping',
        'company_id_list',
        'admin_review_company_id_list',
        'classifier',
        'parser',
        'parser_options',
        'parser_fields_list',
        'search_tags_list',
        'excluded_fields_list',
        't_fieldmap_id',
        'abbyy_label1',
        'abbyy_label2',
        'abbyy_label3',
        'abbyy_label4',
        'abbyy_label5',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id_list' => 'json',
        'admin_review_company_id_list' => 'json',
        'classification' => 'json',
        'mapping' => 'json',
        'parser_options' => 'json',
        'parser_fields_list' => 'json',
        'search_tags_list' => 'json',
        'excluded_fields_list' => 'json',
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

    public function fieldMap()
    {
        return $this->belongsTo(FieldMap::class, 't_fieldmap_id');
    }
}
