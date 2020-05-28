<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderAddressEvent
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property \App\Models\Address address
 * @property \App\Models\Order order
 * @property string address_schedule_description
 * @property integer t_order_id
 * @property integer t_address_id
 * @property boolean t_address_verified
 * @property boolean t_address_raw_text
 * @property integer event_number
 * @property boolean is_hook_event
 * @property boolean is_mount_event
 * @property boolean is_deliver_event
 * @property boolean is_dismount_event
 * @property boolean is_drop_event
 * @property boolean call_for_appointment
 * @property time delivery_window_from_localtime
 * @property time delivery_window_to_localtime
 * @property string delivery_instructions
 * @property \App\Models\Address getAddressAttribute
 */
class OrderAddressEvent extends Model
{
    use SoftDeletes;

    protected $appends = [
        'address'
    ];


    public $table = 't_order_address_events';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'address_schedule_description',
        't_order_id',
        't_address_id',
        't_address_verified',
        'event_number',
        'is_hook_event',
        'is_mount_event',
        'is_deliver_event',
        'is_dismount_event',
        'is_drop_event',
        'call_for_appointment',
        'delivery_window_from_localtime',
        'delivery_window_to_localtime',
        'delivery_instructions',
        'unparsed_event_type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'address_schedule_description' => 'string',
        't_order_id' => 'integer',
        't_address_id' => 'integer',
        't_address_verified' => 'boolean',
        't_address_raw_text' => 'string',
        'event_number' => 'integer',
        'is_hook_event' => 'boolean',
        'is_mount_event' => 'boolean',
        'is_deliver_event' => 'boolean',
        'is_dismount_event' => 'boolean',
        'is_drop_event' => 'boolean',
        'call_for_appointment' => 'boolean',
        'delivery_instructions' => 'string',
        'unparsed_event_type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        't_order_id' => 'required',
        't_address_id' => 'required',
        'event_number' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class, 't_address_id');
    }

    /**
     *
     * @return \App\Models\Address
     */
    public function getAddressAttribute()
    {
        return $this->address()->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 't_order_id');
    }
}
