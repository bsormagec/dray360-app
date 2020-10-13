<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;

class Address extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Address::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'location_name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Addresses';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'latitude',
        'longitude',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postal_code',
        'country',
        'Hours of operation',
        'location_name',
        'location_phone',
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
            Number::make('Latitude', 'latitude')->hideFromIndex(),
            Number::make('Longitude', 'longitude')->hideFromIndex(),
            Text::make('Location name', 'location_name'),
            Text::make('Address line 1', 'address_line_1'),
            Text::make('Address line 2', 'address_line_2')->hideFromIndex(),
            Text::make('City', 'city'),
            Text::make('County', 'county')->hideFromIndex(),
            Text::make('Postal code', 'postal_code'),
            Text::make('Country', 'country'),
            Text::make('Hours of operation', 'hours_of_operation')->hideFromIndex(),
            Text::make('Location phone', 'location_phone'),
            Boolean::make('Is billable', 'is_billable'),
            Boolean::make('Is terminal', 'is_terminal'),
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
