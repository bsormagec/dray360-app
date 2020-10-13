<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;
use App\Models\OCRRequestStatus as OcrRequestStatusModel;

class OcrRequestStatus extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OCRRequestStatus::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'status';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Ocr Entities';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'request_id', 'status', 'order_id', 'company_id'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = [
        'company',
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
        $statuses = collect(OcrRequestStatusModel::STATUS_MAP)
            ->mapWithKeys(fn ($displayName, $key) => [$key => $key])
            ->toArray();

        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Request Id', 'request_id'),
            Select::make('Status', 'status')->options($statuses),
            DateTime::make('Status date', 'status_date'),
            Code::make('Status metadata', 'status_metadata')->json(),
            DateTime::make('Created At', 'created_at')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            BelongsTo::make('Company', 'company', Company::class),
            BelongsTo::make('Order', 'order', Order::class),
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
