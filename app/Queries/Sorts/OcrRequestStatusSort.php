<?php

namespace App\Queries\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class OcrRequestStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->join('t_job_state_changes', 't_job_state_changes.id', '=', 't_job_state_changes_id')
            ->orderBy('t_job_state_changes.status', $direction);
    }
}
