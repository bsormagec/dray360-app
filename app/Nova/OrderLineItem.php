<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;

class OrderLineItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OrderLineItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'contents';

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
        'id', 'contents',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = [
        'order',
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
            BelongsTo::make('Order', 'order', Order::class),
            Text::make('Contents', 'contents'),
            Text::make('Haz class', 'haz_class')->hideFromIndex(),
            Text::make('Haz contact name', 'haz_contact_name')->hideFromIndex(),
            Text::make('Haz description', 'haz_description')->hideFromIndex(),
            Text::make('Haz flashpoint temp', 'haz_flashpoint_temp')->hideFromIndex(),
            Text::make('Haz phone', 'haz_phone')->hideFromIndex(),
            Text::make('Haz qualifier', 'haz_qualifier')->hideFromIndex(),
            Text::make('Haz un code', 'haz_un_code')->hideFromIndex(),
            Text::make('Haz un name', 'haz_un_name')->hideFromIndex(),
            Number::make('Haz flashpoint temp uom', 'haz_flashpoint_temp_uom')->hideFromIndex(),
            Number::make('Haz imdg page number', 'haz_imdg_page_number')->hideFromIndex(),
            Boolean::make('Is hazardous', 'is_hazardous'),
            Text::make('Packaging group', 'packaging_group'),
            Number::make('Quantity', 'quantity'),
            Number::make('Weight', 'weight'),
            Number::make('Total weight', 'total_weight'),
            Text::make('Unit of measure', 'unit_of_measure'),
            Text::make('Weight uom', 'weight_uom'),
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
