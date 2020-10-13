<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class VerifiedAddress extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\VerifiedAddress::class;

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
    public static $group = 'Addresses';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'ocr_address_raw_text',
        'company_address_tms_code',
        'company_address_tms_text',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['company', 'tmsProvider'];

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
            BelongsTo::make('Company')
                ->nullable()
                ->rules('required'),
            BelongsTo::make('Tms Provider', 'tmsProvider', TmsProvider::class)
                ->nullable()
                ->rules('required'),
            Textarea::make('Ocr address rawtext', 'ocr_address_raw_text')
                ->rules('required'),
            Text::make('Company Address Tms Code', 'company_address_tms_code')
                ->rules('required', 'max:512'),
            Textarea::make('Company address rawtext', 'company_address_tms_text')
                ->rules('required'),
            Number::make('Verified count', 'verified_count')
                ->rules('integer'),
            Boolean::make('Skip verification', 'skip_verification')
                ->rules('boolean'),
            Textarea::make('Deleted reason', 'deleted_reason'),
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
