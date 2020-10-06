<?php

namespace App\Queries\Filters;

use App\Models\OCRRequestStatus;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OrderStatusFilter implements Filter
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

        $query->whereHas('ocrRequest.latestOcrRequestStatus', function ($query) use ($values) {
            $query->whereIn('status', $values);
        });
    }
}
