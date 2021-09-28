<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use App\Models\Company as CompanyModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class VAddress extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\VAddress::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'location_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'location_name',
        'address_line_1',
        'city',
        'postal_code',
        'state',
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
            ID::make(__('Address ID'), 'address_id'),
            Text::make('Company', 'company_name'),
            Text::make('TMS', 'tms_name'),
            Text::make('TMS Code', 'tms_code'),
            Text::make('Location Name', 'location_name'),
            Text::make('Address Line 1', 'address_line_1'),
            Text::make('Address Line 2', 'address_line_2'),
            Text::make('City', 'city'),
            Text::make('State', 'state'),
            Text::make('Postal', 'postal_code'),
            Text::make('Created At', 'address_created_at')->hideFromIndex(),
            Text::make('Updated At', 'address_updated_at')->hideFromIndex(),
            Text::make('Deleted At', 'address_deleted_at')->hideFromIndex(),
            Boolean::make('Deleted', 'address_deleted_at')
                ->resolveUsing(function ($deletedAt) {
                    return ! ! $deletedAt;
                }),
            Boolean::make('Terminal', 'is_terminal')->hideFromIndex(),
            Boolean::make('Billable', 'is_billable')->hideFromIndex(),
            Boolean::make('Cc_payor', 'is_cc_payor')->hideFromIndex(),
            Boolean::make('Cc_customer', 'is_cc_customer')->hideFromIndex(),
            Boolean::make('Cc_ssrr', 'is_cc_ssrr')->hideFromIndex(),
            Boolean::make('Cc_carrier', 'is_cc_carrier')->hideFromIndex(),
            Boolean::make('Cc_consignee', 'is_cc_consignee')->hideFromIndex(),
            Boolean::make('Cc_driver', 'is_cc_driver')->hideFromIndex(),
            Boolean::make('Cc_shipper', 'is_cc_shipper')->hideFromIndex(),
            Boolean::make('Cc_vendor', 'is_cc_vendor')->hideFromIndex(),
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
