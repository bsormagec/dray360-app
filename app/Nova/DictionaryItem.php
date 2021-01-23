<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use App\Models\User as UserModel;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Models\TMSProvider as TmsProviderModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class DictionaryItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\DictionaryItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'item_display_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'item_display_name', 'item_key'
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
            Text::make('Item Key', 'item_key')
                ->rules(['required', 'string:512'])
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->sortable(),
            Text::make('Item Display Name', 'item_display_name')
                ->rules(['required', 'string:128'])
                ->sortable(),
            Code::make('Item Value', 'item_value')->json()->nullable(),
            Select::make('Item Type', 'item_type')
                ->options([
                    \App\Models\DictionaryItem::TEMPLATE_TYPE => 'Template',
                    \App\Models\DictionaryItem::ITGCONTAINER_TYPE => 'ITG Container',
                ])
                ->rules([
                    'required',
                    'in:template',
                    ])
                ->sortable(),
            BelongsTo::make('Company', 'company', Company::class)->nullable(),
            BelongsTo::make('Tms Provider', 'tmsProvider', TmsProvider::class)->nullable(),
            BelongsTo::make('User', 'user', User::class)->nullable(),

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
            new BelongsToFilter('User', 'user', UserModel::class, 'name'),
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
