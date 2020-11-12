<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Order $order
 * @property integer $t_order_id
 * @property integer $quantity
 * @property string $unit_of_measure
 * @property string $contents
 * @property string $multiline_contents
 * @property number $weight
 * @property number $total_weight
 * @property string $weight_uom
 * @property boolean $is_hazardous
 * @property string $haz_contact_name
 * @property string $haz_phone
 * @property string $haz_un_code
 * @property string $haz_un_name
 * @property string $haz_class
 * @property string $haz_qualifier
 * @property string $haz_description
 * @property integer $haz_imdg_page_number
 * @property integer $haz_flashpoint_temp
 * @property string $haz_flashpoint_temp_uom
 * @property string $packaging_group
 */
class OrderLineItem extends Model
{
    use SoftDeletes;

    public $table = 't_order_line_items';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_order_id',
        'quantity',
        'unit_of_measure',
        'contents',
        'multiline_contents',
        'weight',
        'total_weight',
        'weight_uom',
        'is_hazardous',
        'haz_contact_name',
        'haz_phone',
        'haz_un_code',
        'haz_un_name',
        'haz_class',
        'haz_qualifier',
        'haz_description',
        'haz_imdg_page_number',
        'haz_flashpoint_temp',
        'haz_flashpoint_temp_uom',
        'packaging_group',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        't_order_id' => 'integer',
        'quantity' => 'integer',
        'unit_of_measure' => 'string',
        'contents' => 'string',
        'multiline_contents' => 'string',
        'weight' => 'float',
        'total_weight' => 'float',
        'weight_uom' => 'string',
        'is_hazardous' => 'boolean',
        'haz_contact_name' => 'string',
        'haz_phone' => 'string',
        'haz_un_code' => 'string',
        'haz_un_name' => 'string',
        'haz_class' => 'string',
        'haz_qualifier' => 'string',
        'haz_description' => 'string',
        'haz_imdg_page_number' => 'integer',
        'haz_flashpoint_temp' => 'integer',
        'haz_flashpoint_temp_uom' => 'string',
        'packaging_group' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = ['t_order_id' => 'required|exist:t_orders,id'];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 't_order_id');
    }
}
