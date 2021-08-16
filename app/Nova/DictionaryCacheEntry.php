<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class DictionaryCacheEntry extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\DictionaryCacheEntry::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'cache_type'
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
            BelongsTo::make('Dictionary Item', 'dictionaryItem', DictionaryItem::class)->searchable()->nullable(),
            BelongsTo::make('Company', 'company', Company::class)->nullable(),
            Select::make('Cache Type', 'cache_type')
                ->options(\App\Models\DictionaryItem::TYPES_LIST_OPTIONS)
                ->rules([
                    'required',
                    ])
                ->sortable(),
            Number::make('Verified Count', 'verified_count')->required(),
            Text::make('Cached variant name', 'cached_variant_name')->sortable()->hideFromIndex(),
            Text::make('Cached bill to address raw text', 'cached_bill_to_address_raw_text')->sortable()->hideFromIndex(),
            Text::make('Cached event1 address raw text', 'cached_event1_address_raw_text')->sortable()->hideFromIndex(),
            Text::make('Cached event2 address raw text', 'cached_event2_address_raw_text')->sortable()->hideFromIndex(),
            Text::make('Cached event3 address raw text', 'cached_event3_address_raw_text')->sortable()->hideFromIndex(),
            Text::make('Cached hazardous', 'cached_hazardous')->sortable()->hideFromIndex(),
            Text::make('Cached equipment size', 'cached_equipment_size')->sortable()->hideFromIndex(),
            Text::make('Cached vessel', 'cached_vessel')->sortable()->hideFromIndex(),
            Text::make('Cached carrier', 'cached_carrier')->sortable()->hideFromIndex(),
            Text::make('Cached shipment direction', 'cached_shipment_direction')->sortable()->hideFromIndex(),
            Text::make('Cached template key', 'cached_template_key')->sortable()->hideFromIndex(),
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
