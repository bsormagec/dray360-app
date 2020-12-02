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
        'id', 'request_id'
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
            BelongsTo::make('Bill to address', 'billToAddress', Address::class)
                ->searchable()
                ->nullable(),
            Boolean::make('Bill to address verified', 'bill_to_address_verified')
                ->hideFromIndex()
                ->nullable(),
            Text::make('Bill to address raw text', 'bill_to_address_raw_text')
                ->hideFromIndex()
                ->nullable(),
            BelongsTo::make('Port ramp of origin', 'portRampOfOriginAddress', Address::class)
                ->hideFromIndex()
                ->searchable()
                ->nullable(),
            Boolean::make('Port ramp of origin address verified', 'port_ramp_of_origin_address_verified')
                ->hideFromIndex()
                ->nullable(),
            Text::make('Port ramp of origin address raw text', 'port_ramp_of_origin_address_raw_text')
                ->hideFromIndex()
                ->nullable(),
            BelongsTo::make('Port ramp of destination', 'portRampOfDestinationAddress', Address::class)
                ->hideFromIndex()
                ->searchable()
                ->nullable(),
            Boolean::make('Port ramp of destination address verified', 'port_ramp_of_destination_address_verified')
                ->hideFromIndex()
                ->nullable(),
            Text::make('Port ramp of destination address raw text', 'port_ramp_of_destination_address_raw_text')
                ->hideFromIndex()
                ->nullable(),
            BelongsTo::make('Company', 'company', Company::class),
            BelongsTo::make('Tms Provider', 'tmsProvider', TmsProvider::class),
            BelongsTo::make('Equipment Type', 'equipmentType', EquipmentType::class)
                ->searchable()
                ->nullable(),
            Text::make('Shipment designation', 'shipment_designation')->hideFromIndex()->nullable(),
            Text::make('Equipment type', 'equipment_type')->hideFromIndex()->nullable(),
            Text::make('Shipment direction', 'shipment_direction')->nullable(),
            Boolean::make('One way', 'one_way')->hideFromIndex()->nullable(),
            Boolean::make('Yard pre pull', 'yard_pre_pull')->hideFromIndex()->nullable(),
            Boolean::make('Has chassis', 'has_chassis')->hideFromIndex()->nullable(),
            Text::make('Unit number', 'unit_number')->hideFromIndex()->nullable(),
            Text::make('Equipment size', 'equipment_size')->hideFromIndex()->nullable(),
            Boolean::make('Hazardous', 'hazardous')->hideFromIndex()->nullable(),
            Text::make('Reference number', 'reference_number')->nullable(),
            Text::make('Rate quote number', 'rate_quote_number')->hideFromIndex()->nullable(),
            Text::make('Seal number', 'seal_number')->hideFromIndex()->nullable(),
            Text::make('Vessel', 'vessel')->hideFromIndex()->nullable(),
            Text::make('Voyage', 'voyage')->hideFromIndex()->nullable(),
            Text::make('Master bol mawb', 'master_bol_mawb')->hideFromIndex()->nullable(),
            Text::make('House bol hawb', 'house_bol_hawb')->hideFromIndex()->nullable(),
            DateTime::make('Estimated arrival utc', 'estimated_arrival_utc')->hideFromIndex()->nullable(),
            DateTime::make('Last free date utc', 'last_free_date_utc')->hideFromIndex()->nullable(),
            Text::make('Booking number', 'booking_number')->hideFromIndex()->nullable(),
            Text::make('Bill of lading', 'bill_of_lading')->nullable(),
            Code::make('Ocr data', 'ocr_data')->json()->nullable(),
            Text::make('Pickup number', 'pickup_number')->hideFromIndex()->nullable(),
            Text::make('Variant Id', 'variant_id')->hideFromIndex()->nullable(),
            Text::make('Variant name', 'variant_name')->hideFromIndex()->nullable(),
            Text::make('Tms shipment id', 'tms_shipment_id')->nullable(),
            Text::make('Carrier', 'carrier')->hideFromIndex()->nullable(),
            Number::make('Bill charge', 'bill_charge')->hideFromIndex()->nullable(),
            Text::make('Bill comment', 'bill_comment')->hideFromIndex()->nullable(),
            Number::make('Line haul', 'line_haul')->hideFromIndex()->nullable(),
            Text::make('Rate box', 'rate_box')->hideFromIndex()->nullable(),
            Number::make('Fuel surcharge', 'fuel_surcharge')->hideFromIndex()->nullable(),
            Number::make('Total accessorial charges', 'total_accessorial_charges')->hideFromIndex()->nullable(),
            Text::make('Equipment provider', 'equipment_provider')->hideFromIndex()->nullable(),
            Text::make('Actual destination', 'actual_destination')->hideFromIndex()->nullable(),
            Text::make('Actual origin', 'actual_origin')->hideFromIndex()->nullable(),
            Text::make('Customer number', 'customer_number')->hideFromIndex()->nullable(),
            Boolean::make('Expedite', 'expedite')->hideFromIndex()->nullable(),
            Text::make('Load number', 'load_number')->hideFromIndex()->nullable(),
            Text::make('Purchase order number', 'purchase_order_number')->hideFromIndex()->nullable(),
            Text::make('Release number', 'release_number')->hideFromIndex()->nullable(),
            Text::make('Ship comment', 'ship_comment')->hideFromIndex()->nullable(),
            Text::make('Division code', 'division_code')->hideFromIndex()->nullable(),
            DateTime::make('Pickup by date', 'pickup_by_date')->hideFromIndex()->nullable(),
            Text::make('Pickup by time', 'pickup_by_time')->hideFromIndex()->nullable(),
            DateTime::make('Cutoff date', 'cutoff_date')->hideFromIndex()->nullable(),
            Text::make('Cutoff time', 'cutoff_time')->hideFromIndex()->nullable(),
            HasMany::make('Order Address Events', 'orderAddressEvents', OrderAddressEvent::class),
            HasMany::make('Order Line Items', 'orderLineItems', OrderLineItem::class),
            Text::make('TMS Template ID', 'tms_template_id')->hideFromIndex()->nullable(),

            BelongsTo::make('Preceded by Order', 'precededByOrder', Order::class)->nullable(),
            BelongsTo::make('Succeded by Order', 'succededByOrder', Order::class)->nullable(),
            DateTime::make('TMS Submission Date', 'tms_submission_datetime')->hideFromIndex()->nullable(),
            DateTime::make('TMS Cancelled Date', 'tms_cancelled_datetime')->hideFromIndex()->nullable(),
            DateTime::make('Cancelled Date', 'cancelled_datetime')->hideFromIndex()->nullable(),

            Number::make('Interchange Count', 'interchange_count')
                ->min(0)
                ->max(10000000)
                ->step(1.0)
                ->hideFromIndex()
                ->nullable(),
            Number::make('Interchange Error Count', 'interchange_err_count')
                ->min(0)
                ->max(10000000)
                ->step(1.0)
                ->hideFromIndex()
                ->nullable(),
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
