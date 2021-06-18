<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;

class OcrVariant extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OCRVariant::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'abbyy_variant_name';

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
        'abbyy_variant_id', 'abbyy_variant_name', 'description', 'id',
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
            Text::make('Abbyy variant id', 'abbyy_variant_id')->sortable(),
            Text::make('Abbyy variant name', 'abbyy_variant_name')->sortable(),
            Text::make('Description', 'description')->sortable(),
            Text::make('Variant Type', 'variant_type')->sortable(),
            Text::make('Classifier', 'classifier')->sortable(),
            Text::make('Parser', 'parser')->sortable(),
            Text::make('Abbyy Label 1', 'abbyy_label1')->sortable(),
            Text::make('Abbyy Label 2', 'abbyy_label2')->sortable(),
            Text::make('Abbyy Label 3', 'abbyy_label3')->sortable(),
            Text::make('Abbyy Label 4', 'abbyy_label4')->sortable(),
            Text::make('Abbyy Label 5', 'abbyy_label5')->sortable(),
            Code::make('Mapping', 'mapping')->json()->rules(['nullable', 'json']),
            Code::make('Company ID List (csv/edi uploads)', 'company_id_list')->json()->rules(['nullable', 'json']),
            // Code::make('Company ID List (enable admin review)', 'admin_review_company_id_list')->json()->rules(['nullable', 'json']), // no longer using the column
            Code::make('Classification', 'classification')->json()->rules(['nullable', 'json']),
            Code::make('Parser Options', 'parser_options')->json()->rules(['nullable', 'json']),
            Code::make('Parser Fields List', 'parser_fields_list')->json()->rules(['nullable', 'json']),
            Code::make('Search Tags List', 'search_tags_list')->json()->rules(['nullable', 'json']),
            Code::make('Excluded Fields List', 'excluded_fields_list')->json()->rules(['nullable', 'json']),
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
