<?php

namespace App\Models;

use App\Models\Traits\MapsAudits;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Order $order
 * @property integer $t_order_id
 * @property integer $quantity
 * @property string $contents
 * @property string $multiline_contents
 * @property number $weight
 * @property number $total_weight
 * @property string $weight_uom
 * @property boolean $is_hazardous
 */
class OrderLineItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use MapsAudits;
    use SoftDeletes;

    public $table = 't_order_line_items';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        't_order_id',
        'quantity',
        'contents',
        'multiline_contents',
        'weight',
        'total_weight',
        'weight_uom',
        'is_hazardous',
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
        'weight' => 'float',
        'total_weight' => 'float',
        'is_hazardous' => 'boolean',
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
