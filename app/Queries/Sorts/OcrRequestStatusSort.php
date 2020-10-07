<?php

namespace App\Queries\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class OcrRequestStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->join('t_job_state_changes as sort_status', 'sort_status.id', '=', 't_job_state_changes_id')
            ->orderBy('sort_status.status', $direction);
    }
}
