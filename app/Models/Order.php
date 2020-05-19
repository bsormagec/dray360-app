<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Illuminate\Support\Facades\DB;

/**
 * Class Order
 * @package App\Models
 * @version March 5, 2020, 8:00 pm UTC
 *
 * @property string request_id
 * @property string shipment_designation
 * @property string equipment_type
 * @property string shipment_direction
 * @property boolean one_way
 * @property boolean yard_pre_pull
 * @property boolean has_chassis
 * @property string unit_number
 * @property string equipment
 * @property string equipment_size
 * @property string owner_or_ss_company
 * @property boolean hazardous
 * @property boolean expedite_shipment
 * @property string reference_number
 * @property string rate_quote_number
 * @property string seal_number_list
 * @property string port_ramp_of_origin
 * @property string port_ramp_of_destination
 * @property string vessel
 * @property string voyage
 * @property string master_bol_mawb
 * @property string house_bol_hawb
 * @property string|\Carbon\Carbon estimated_arrival_utc
 * @property string|\Carbon\Carbon last_free_date_utc
 * @property string booking_number
 * @property string pickup_number
 * @property string bol
 * @property json ocr_data
 *
 * @property \Illuminate\Database\Eloquent\Collection orderAddressEvents
 * @property \Illuminate\Database\Eloquent\Collection orderLineItems
 * @property \Illuminate\Database\Eloquent\Collection ocrRequest
 * @property \Illuminate\Database\Eloquent\Collection getOCRRequestStatusList
 * @property \App\Models\Address billToAddress
 * @property \App\Models\Address portRampOfOriginAddress
 * @property \App\Models\Address portRampOfDestinationAddress

 * @property \App\Models\OCRRequest getOCRRequestAttribute
 * @property \App\Models\OCRRequestStatus getLatestOCRRequestStatusAttribute
 * @property \App\Model\OrderLineItem getOrderLineItemsAttribute
 * @property \Illuminate\Database\Eloquent\Collection getOrderAddressEventsAttribute
 * @property \Illuminate\Database\Eloquent\Collection getOCRRequestAttribute
 * @property \App\Models\Address getBillToAddressAttribute
 * @property \App\Models\Address getPortRampOfOriginAddressAttribute
 * @property \App\Models\Address getPortRampOfDestinationAddressAttribute
 */



class Order extends Model
{
    use SoftDeletes;

    protected $appends = [
        'ocr_request',
        'latest_ocr_request_status',
        'order_address_events',
        'order_line_items',
        'bill_to_address',
        'port_ramp_of_origin_address',
        'port_ramp_of_destination_address'
    ];

    public $table = 't_orders';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'request_id',
        'shipment_designation',
        'equipment_type',
        'shipment_direction',
        'one_way',
        'yard_pre_pull',
        'has_chassis',
        'unit_number',
        'equipment',
        'equipment_size',
        'owner_or_ss_company',
        'hazardous',
        'expedite_shipment',
        'reference_number',
        'rate_quote_number',
        'seal_number_list',
        'port_ramp_of_origin',
        'port_ramp_of_destination',
        'vessel',
        'voyage',
        'master_bol_mawb',
        'house_bol_hawb',
        'estimated_arrival_utc',
        'last_free_date_utc',
        'booking_number',
        'pickup_number'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'request_id' => 'string',
        'shipment_designation' => 'string',
        'equipment_type' => 'string',
        'shipment_direction' => 'string',
        'one_way' => 'boolean',
        'yard_pre_pull' => 'boolean',
        'has_chassis' => 'boolean',
        'unit_number' => 'string',
        'equipment' => 'string',
        'equipment_size' => 'string',
        'owner_or_ss_company' => 'string',
        'hazardous' => 'boolean',
        'expedite_shipment' => 'boolean',
        'reference_number' => 'string',
        'rate_quote_number' => 'string',
        'seal_number_list' => 'string',
        'port_ramp_of_origin' => 'string',
        'port_ramp_of_destination' => 'string',
        'vessel' => 'string',
        'voyage' => 'string',
        'master_bol_mawb' => 'string',
        'house_bol_hawb' => 'string',
        'estimated_arrival_utc' => 'datetime',
        'last_free_date_utc' => 'datetime',
        'booking_number' => 'string',
        'pickup_number' => 'string',
        'bol' => 'string',
        'bill_to_address_id' => 'integer',
        'port_ramp_of_origin_address_id' => 'integer',
        'port_ramp_of_destination_address_id' => 'integer',
        'ocr_data' => 'json'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function orderAddressEvents()
    {
        return $this->hasMany(\App\Models\OrderAddressEvent::class, 't_order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function orderLineItems()
    {
        return $this->hasMany(\App\Models\OrderLineItem::class, 't_order_id');
    }

    /**
     *
     * @return \App\Models\OrderLineItem
     */
    function getOrderLineItemsAttribute() {
        return $this->orderLineItems()->get();
    }

    /**
     *
     * @return \App\Models\OrderAddressEvent
     */
    function getOrderAddressEventsAttribute() {
        return $this->orderAddressEvents()->get();
    }

    /**
     * The OCR request relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function ocrRequest()
    {
        return $this->hasOne(\App\Models\OCRRequest::class, 'request_id', 'request_id');
    }

    /**
     * Get the OCR request attribute
     *
     * @return \Models\OCRRequest
     */
    public function getOCRRequestAttribute() {
        return $this->ocrRequest()->get()[0];
    }

    /**
     * Returns the OCRRequestStatus list
     *
     * @return collection
     */
    public function getOCRRequestStatusList() {
        return $this->getOCRRequestAttribute()->statusList()->get();
    }


    /**
     * Returns the latest OCRRequestStatus object
     *
     * @return OCRRequestStatus
     */
    public function getLatestOCRRequestStatusAttribute() {
        return $this->getOCRRequestAttribute()->latestOCRRequestStatus();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function billToAddress()
    {
        return $this->belongsTo(\App\Models\Address::class, 'bill_to_address_id');
    }

    /**
     *
     * @return \App\Models\Address
     */
    function getBillToAddressAttribute() {
        return $this->billToAddress()->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function portRampOfOriginAddress()
    {
        return $this->belongsTo(\App\Models\Address::class, 'port_ramp_of_origin_address_id');
    }

    /**
     *
     * @return \App\Models\Address
     */
    function getPortRampOfOriginAddressAttribute() {
        return $this->portRampOfOriginAddress()->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function portRampOfDestinationAddress()
    {
        return $this->belongsTo(\App\Models\Address::class, 'port_ramp_of_destination_address_id');
    }

    /**
     *
     * @return \App\Models\Address
     */
    function getPortRampOfDestinationAddressAttribute() {
        return $this->portRampOfDestinationAddress()->get();
    }

}
