<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use App\Models\DictionaryItem;
use Laravel\Nova\Filters\Filter;

class DictionaryItemTypeFilter extends Filter
{
    public $name = 'Item Type';
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('item_type', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return collect(DictionaryItem::TYPES_LIST_OPTIONS)
            ->mapWithKeys(fn ($item, $key) => [$item => $key])
            ->toArray();
    }
}
