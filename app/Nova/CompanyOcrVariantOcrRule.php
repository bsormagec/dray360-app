<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use App\Models\Company as CompanyModel;
use App\Models\OCRRule as OCRRuleModel;
use App\Models\OCRVariant as OCRVariantModel;
use App\Nova\Filters\BelongsTo as BelongsToFilter;

class CompanyOcrVariantOcrRule extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = \App\Models\CompanyOCRVariantOCRRule::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     */
    public static $search = [];

    /**
     * The relationships that should be eager loaded on index queries.
     */
    public static $with = [
        'company',
        'ocrRule',
        'ocrVariant',
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
            BelongsTo::make('Company', 'company', Company::class)->sortable(),
            BelongsTo::make('Variant', 'ocrVariant', OcrVariant::class)->sortable()->searchable(),
            BelongsTo::make('Rule', 'ocrRule', OcrRule::class)->sortable()->searchable(),
            Text::make('Sequence', 'rule_sequence')->sortable(),
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
            new BelongsToFilter('Variant', 'ocrVariant', OCRVariantModel::class, 'abbyy_variant_name'),
            new BelongsToFilter('Rule', 'ocrRule', OCRRuleModel::class, 'name'),
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
