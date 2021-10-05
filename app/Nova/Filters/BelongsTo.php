<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BelongsTo extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    protected string $relationMethod;
    protected string $optionsModel;
    protected string $displayColumn;

    public function __construct(string $name, string $relationMethod, string $model, string $displayColumn)
    {
        $this->name = $name;
        $this->relationMethod = $relationMethod;
        $this->optionsModel = $model;
        $this->displayColumn = $displayColumn;
    }

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
        return $query->whereHas($this->relationMethod, function ($query) use ($value) {
            $query->where('id', $value);
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return $this->optionsModel::query()
            ->orderBy($this->displayColumn)
            ->pluck('id', $this->displayColumn);
    }

    /**
     * Get the key for the filter.
     */
    public function key()
    {
        return 'belongsto_'.$this->name;
    }
}
