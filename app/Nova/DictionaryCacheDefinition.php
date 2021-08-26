<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;

class DictionaryCacheDefinition extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\DictionaryCacheDefinition::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'cache_type';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'cache_type', 'id'
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
            Select::make('Cache Type', 'cache_type')
                ->options(\App\Models\DictionaryItem::TYPES_LIST_OPTIONS)
                ->rules(['required'])
                ->sortable(),
            Boolean::make('Use variant name', 'use_variant_name'),
            Boolean::make('Use bill to address raw text', 'use_bill_to_address_raw_text'),
            Boolean::make('Use event1 address raw text', 'use_event1_address_raw_text'),
            Boolean::make('Use event2 address raw text', 'use_event2_address_raw_text'),
            Boolean::make('Use event3 address raw text', 'use_event3_address_raw_text'),
            Boolean::make('Use hazardous', 'use_hazardous'),
            Boolean::make('Use equipment size', 'use_equipment_size'),
            Boolean::make('Use vessel', 'use_vessel'),
            Boolean::make('Use carrier', 'use_carrier'),
            Boolean::make('Use shipment direction', 'use_shipment_direction'),
            Boolean::make('Use template key', 'use_template_key'),
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
