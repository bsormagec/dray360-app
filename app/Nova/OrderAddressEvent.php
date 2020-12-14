<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;

class OrderAddressEvent extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OrderAddressEvent::class;

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
        'id',
        't_order_id',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = [
        'order',
        'address',
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
            Text::make('Address schedule description', 'address_schedule_description')->hideFromIndex(),
            BelongsTo::make('Order', 'order', Order::class),
            BelongsTo::make('Address', 'address', Address::class),
            Text::make('Event number', 'event_number'),
            Boolean::make('Is hook event', 'is_hook_event')->hideFromIndex(),
            Boolean::make('Is mount event', 'is_mount_event')->hideFromIndex(),
            Boolean::make('Is deliver event', 'is_deliver_event')->hideFromIndex(),
            Boolean::make('Is dismount event', 'is_dismount_event')->hideFromIndex(),
            Boolean::make('Is pickup event', 'is_pickup_event')->hideFromIndex(),
            Boolean::make('Is drop event', 'is_drop_event')->hideFromIndex(),
            Text::make('Call for appointment', 'call_for_appointment')->hideFromIndex(),
            Text::make('Delivery window from localtime', 'delivery_window_from_localtime')->hideFromIndex(),
            Text::make('Delivery window to localtime', 'delivery_window_to_localtime')->hideFromIndex(),
            Text::make('Delivery instructions', 'delivery_instructions')->hideFromIndex(),
            Text::make('Unparsed event type', 'unparsed_event_type'),
            Boolean::make('Address verified', 't_address_verified'),
            Text::make('Address raw text', 't_address_raw_text'),
            Textarea::make('Notes', 'note'),
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
        return [];
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
