<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class EquipmentType extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\EquipmentType::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'equipment_display';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'equipment_display',
        'equipment_type_and_size',
        'equipment_type',
        'equipment_size',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = [
        'company',
        'tmsProvider',
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
            BelongsTo::make('Company', 'company', Company::class)
                ->sortable()
                ->rules([
                    'required'
                ]),
            BelongsTo::make('Tms provider', 'tmsProvider', TmsProvider::class)
                ->sortable()
                ->rules([
                    'required'
                ]),
            Text::make('Equipment id', 'tms_equipment_id')
                ->sortable()
                ->rules([
                    'required'
                ]),
            Text::make('Equipment owner', 'equipment_owner')
                ->sortable()
                ->rules([
                    'required'
                ]),
            Select::make('Row type', 'row_type')
                ->options(['combined' => 'Combined', 'separate' => 'Separate'])
                ->sortable()
                ->rules([
                    'required',
                    'in:combined,separate',
                ]),
            Text::make('Equipment type & size', 'equipment_type_and_size'),
            Text::make('Equipment type', 'equipment_type'),
            Text::make('Equipment size', 'equipment_size'),
            Code::make('Prefix List', 'line_prefix_list')->json(),
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
            new BelongsToFilter('Company', 'company', \App\Models\Company::class, 'name'),
            new BelongsToFilter('Tms Provider', 'tmsProvider', \App\Models\TMSProvider::class, 'name'),
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
