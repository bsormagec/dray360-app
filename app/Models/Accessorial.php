<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $t_order_id
 * @property string $rulesngine_name
 * @property decimal $amount
 *
 */
class Accessorial extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at';


    public $table = 't_order_accessorials';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'rulesngine_name',
        'amount',
        't_order_id',
        'id'
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        't_order_id' => 'integer',
        'rulesngine_name' => 'string',
        'amount' => 'decimal'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'rulesngine_name' => 'required',
        't_address_id' => 'required',
        'amount' => 'required'
    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 't_order_id');
    }
}
