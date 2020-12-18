<?php

namespace App\Models;

use App\Events\AddressVerified;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Address $address
 * @property \App\Models\Order $order
 * @property string $address_schedule_description
 * @property integer $t_order_id
 * @property integer $t_address_id
 * @property boolean $t_address_verified
 * @property boolean $t_address_raw_text
 * @property integer $event_number
 * @property boolean $is_hook_event
 * @property boolean $is_mount_event
 * @property boolean $is_deliver_event
 * @property boolean $is_dismount_event
 * @property boolean $is_drop_event
 * @property boolean $is_pickup_event
 * @property boolean $call_for_appointment
 * @property string $delivery_window_from_localtime
 * @property string $delivery_window_to_localtime
 * @property string delivery_instructions
 * @property string $unparsed_event_type
 */
class OrderAddressEvent extends Model
{
    use SoftDeletes;

    public $table = 't_order_address_events';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'address_schedule_description',
        'call_for_appointment',
        'delivery_instructions',
        'delivery_window_from_localtime',
        'delivery_window_to_localtime',
        'event_number',
        'is_deliver_event',
        'is_dismount_event',
        'is_drop_event',
        'is_pickup_event',
        'is_hook_event',
        'is_mount_event',
        't_address_id',
        't_address_raw_text',
        't_address_verified',
        't_order_id',
        'unparsed_event_type',
        'note',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'address_schedule_description' => 'string',
        't_address_verified' => 'boolean',
        'event_number' => 'integer',
        'is_hook_event' => 'boolean',
        'is_mount_event' => 'boolean',
        'is_deliver_event' => 'boolean',
        'is_dismount_event' => 'boolean',
        'is_drop_event' => 'boolean',
        'is_pickup_event' => 'boolean',
        'call_for_appointment' => 'boolean',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        't_order_id' => 'required',
        't_address_id' => 'required',
        'event_number' => 'required'
    ];

    public static function booted()
    {
        static::updated(function ($orderAddressEvent) {
            if (
                $orderAddressEvent->getOriginal('t_address_verified') == false
                && $orderAddressEvent->t_address_verified == true
            ) {
                AddressVerified::dispatch($orderAddressEvent);
            }
        });
    }

    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class, 't_address_id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 't_order_id');
    }
}
