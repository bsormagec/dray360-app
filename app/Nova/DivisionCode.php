<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Models\TMSProvider as TmsProviderModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class DivisionCode extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\DivisionCode::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'division_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'division_code', 'division_name'];

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
            Text::make('Division Code', 'division_code')
                ->rules('required')
                ->sortable(),
            Text::make('Division Name', 'division_name')
                ->rules('required')
                ->sortable(),
            BelongsTo::make('Company', 'company', Company::class)->nullable(),
            BelongsTo::make('Tms Provider', 'tmsProvider', TmsProvider::class)->nullable(),

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
