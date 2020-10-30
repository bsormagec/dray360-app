<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Models\TMSProvider as TmsProviderModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class Order extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Order::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Orders';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'request_id', 'status'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = [
        'company',
        'tmsProvider',
        'billToAddress',
        'equipmentType',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Request Id', 'request_id'),
            BelongsTo::make('Bill to address', 'billToAddress', Address::class),
            Boolean::make('Bill to address verified', 'bill_to_address_verified')->hideFromIndex(),
            Text::make('Bill to address raw text', 'bill_to_address_raw_text')->hideFromIndex(),
            BelongsTo::make('Port ramp of origin', 'portRampOfOriginAddress', Address::class)->hideFromIndex(),
            Boolean::make('Port ramp of origin address verified', 'port_ramp_of_origin_address_verified')->hideFromIndex(),
            Text::make('Port ramp of origin address raw text', 'port_ramp_of_origin_address_raw_text')->hideFromIndex(),
            BelongsTo::make('Port ramp of destination', 'portRampOfDestinationAddress', Address::class)->hideFromIndex(),
            Boolean::make('Port ramp of destination address verified', 'port_ramp_of_destination_address_verified')->hideFromIndex(),
            Text::make('Port ramp of destination address raw text', 'port_ramp_of_destination_address_raw_text')->hideFromIndex(),
            BelongsTo::make('Company', 'company', Company::class),
            BelongsTo::make('Tms Provider', 'tmsProvider', TmsProvider::class),
            BelongsTo::make('Equipment Type', 'equipmentType', EquipmentType::class),
            Text::make('Shipment designation', 'shipment_designation')->hideFromIndex(),
            Text::make('Equipment type', 'equipment_type')->hideFromIndex(),
            Text::make('Shipment direction', 'shipment_direction'),
            Boolean::make('One way', 'one_way')->hideFromIndex(),
            Boolean::make('Yard pre pull', 'yard_pre_pull')->hideFromIndex(),
            Boolean::make('Has chassis', 'has_chassis')->hideFromIndex(),
            Text::make('Unit number', 'unit_number')->hideFromIndex(),
            Text::make('Equipment', 'equipment')->hideFromIndex(),
            Text::make('Equipment size', 'equipment_size')->hideFromIndex(),
            Text::make('Owner or ss company', 'owner_or_ss_company')->hideFromIndex(),
            Boolean::make('Hazardous', 'hazardous')->hideFromIndex(),
            Text::make('Reference number', 'reference_number'),
            Text::make('Rate quote number', 'rate_quote_number')->hideFromIndex(),
            Text::make('Seal number', 'seal_number')->hideFromIndex(),
            Text::make('Vessel', 'vessel')->hideFromIndex(),
            Text::make('Voyage', 'voyage')->hideFromIndex(),
            Text::make('Master bol mawb', 'master_bol_mawb')->hideFromIndex(),
            Text::make('House bol hawb', 'house_bol_hawb')->hideFromIndex(),
            DateTime::make('Estimated arrival utc', 'estimated_arrival_utc')->hideFromIndex(),
            DateTime::make('Last free date utc', 'last_free_date_utc')->hideFromIndex(),
            Text::make('Booking number', 'booking_number')->hideFromIndex(),
            Text::make('Bill of lading', 'bill_of_lading'),
            Code::make('Ocr data', 'ocr_data')->json(),
            Text::make('Pickup number', 'pickup_number')->hideFromIndex(),
            Text::make('Variant Id', 'variant_id')->hideFromIndex(),
            Text::make('Variant name', 'variant_name')->hideFromIndex(),
            Text::make('Tms shipment id', 'tms_shipment_id'),
            Text::make('Carrier', 'carrier')->hideFromIndex(),
            Number::make('Bill charge', 'bill_charge')->hideFromIndex(),
            Text::make('Bill comment', 'bill_comment')->hideFromIndex(),
            Number::make('Line haul', 'line_haul')->hideFromIndex(),
            Text::make('Rate box', 'rate_box')->hideFromIndex(),
            Number::make('Fuel surcharge', 'fuel_surcharge')->hideFromIndex(),
            Number::make('Total accessorial charges', 'total_accessorial_charges')->hideFromIndex(),
            Text::make('Equipment provider', 'equipment_provider')->hideFromIndex(),
            Text::make('Actual destination', 'actual_destination')->hideFromIndex(),
            Text::make('Actual origin', 'actual_origin')->hideFromIndex(),
            Text::make('Customer number', 'customer_number')->hideFromIndex(),
            Boolean::make('Expedite', 'expedite')->hideFromIndex(),
            Text::make('Load number', 'load_number')->hideFromIndex(),
            Text::make('Purchase order number', 'purchase_order_number')->hideFromIndex(),
            Text::make('Release number', 'release_number')->hideFromIndex(),
            Text::make('Ship comment', 'ship_comment')->hideFromIndex(),
            Text::make('Division code', 'division_code')->hideFromIndex(),
            DateTime::make('Pickup by date', 'pickup_by_date')->hideFromIndex(),
            Text::make('Pickup by time', 'pickup_by_time')->hideFromIndex(),
            HasMany::make('Order Address Events', 'orderAddressEvents', OrderAddressEvent::class),
            HasMany::make('Order Line Items', 'orderLineItems', OrderLineItem::class),

            BelongsTo::make('Preceded by Order', 'precededByOrder', Order::class),
            BelongsTo::make('Succeded by Order', 'succededByOrder', Order::class),
            DateTime::make('TMS Submission Date', 'tms_submission_datetime')->hideFromIndex(),
            DateTime::make('TMS Cancelled Date', 'tms_cancelled_datetime')->hideFromIndex(),
            DateTime::make('Cancelled Date', 'cancelled_datetime')->hideFromIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new BelongsToFilter('Company', 'company', CompanyModel::class, 'name'),
            new BelongsToFilter('Tms Provider', 'tmsProvider', TmsProviderModel::class, 'name'),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
