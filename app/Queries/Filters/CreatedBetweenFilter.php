<?php

namespace App\Queries\Filters;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CreatedBetweenFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $columnName = Str::contains($property, 'created_at') ? $property : 'created_at';

        if (count($value) < 2) {
            throw ValidationException::withMessages([
                'created_between' => 'Requires two dates separated by comma. ex 2020-01-01,2020-01-02'
            ]);
        }
        $dates = [
            (new Carbon($value[0]))->startOfDay(),
            (new Carbon($value[1]))->endOfDay(),
        ];

        return $query->whereBetween($columnName, $dates);
    }
}
