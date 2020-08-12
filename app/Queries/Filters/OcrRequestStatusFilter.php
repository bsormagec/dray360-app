<?php

namespace App\Queries\Filters;

use App\Models\OCRRequestStatus;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OcrRequestStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $values = is_array($value) ? $value : [$value];

        if ($property == 'display_status') {
            $values = collect($values)
                ->map(fn ($value) => OCRRequestStatus::getStatusFromDisplayStatus($value))
                ->flatten()
                ->toArray();
        }

        $query->leftJoin('t_orders', function ($query) use ($values) {
            $query->on('t_orders.id', '=', 't_job_latest_state.order_id')
                ->whereExists(function ($query) use ($values) {
                    $query->select('id')
                        ->from('t_job_state_changes')
                        ->whereColumn('t_job_latest_state.t_job_state_changes_id', 't_job_state_changes.id')
                        ->whereIn('status', $values);
                });
        })
        ->whereHas('latestOcrRequestStatus', function ($query) use ($values) {
            $query->whereIn('status', $values);
        });
    }
}
