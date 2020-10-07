<?php

namespace App\Queries\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class OrderStatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query
            ->join('t_job_latest_state as l', 'l.order_id', '=', 't_orders.id')
            ->join('t_job_state_changes as s', 's.id', '=', 'l.t_job_state_changes_id')
            ->orderBy('s.status', $direction);
    }
}
