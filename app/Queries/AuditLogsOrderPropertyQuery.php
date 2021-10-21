<?php

namespace App\Queries;

use Closure;
use App\Models\Order;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AuditLogsOrderPropertyQuery extends QueryBuilder
{
    public function __construct(array $filters)
    {
        $property = $filters['property'];

        $query = Audit::query()
            ->select([
                DB::raw("'{$property}' as property_name"),
                DB::raw("replace(json_unquote(json_extract(old_values, '$.{$property}')), '\n', '') as old_value"),
                DB::raw("replace(json_unquote(json_extract(new_values, '$.{$property}')), '\n', '') as new_value"),
                'c.name as company_name',
                'c.id as company_id',
                'o.request_id as request_id',
                'o.id as order_id',
                'o.created_at as order_date',
                DB::raw("json_unquote(json_extract(o.ocr_data, '$.fields.last_editor.value')) as verifier"),
                DB::raw("coalesce(o.variant_id, '') as variant_id"),
                DB::raw("left(o.variant_name, 30) as variant_name"),
                DB::raw("audits.id as audit_id"),
                'audits.created_at as edit_date',
                'audits.user_id',
                'audits.user_type',
            ])
            ->join('t_orders as o', function ($join) {
                $join->on('audits.auditable_id', '=', 'o.id')
                    ->where('audits.auditable_type', Order::class);
            })
            ->join('t_companies as c', 'o.t_company_id', '=', 'c.id')
            ->with('user', fn ($q) => $q->select(['id', 'name'])->with('roles:id,display_name'))
            ->where('audits.auditable_type', Order::class)
            ->where('audits.new_values', 'like', "%{$property}%")
            ->where('audits.old_values', 'like', "%{$property}%")
            ->whereRaw("json_extract(audits.old_values, '$.{$property}') <> json_extract(audits.new_values, '$.{$property}')")
            ->where(function ($query) use ($property) {
                $query->orWhere('audits.old_values', 'not like', "%\"{$property}\": null%")
                    ->orWhere('audits.new_values', 'not like', "%\"{$property}\": null%");
            })
            ->where($this->getDateRangeFilterQuery($filters))
            ->where('c.name', 'not like', '%onboard%')
            ->where('c.name', 'not like', '%demo%')
            ->where('audits.event', '!=', 'created')
            ->whereNotNull('o.tms_shipment_id');

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::exact('variant_id', 'o.variant_id', false),
            AllowedFilter::exact('company_id', 'o.t_company_id', false),
            AllowedFilter::partial('request_id', 'o.request_id', false),
            AllowedFilter::exact('user_id', 'audits.user_id', false),
        ])
        ->defaultSort('-audit_id')
        ->allowedSorts([
            AllowedSort::field('old_value'),
            AllowedSort::field('new_value'),
            AllowedSort::field('company_name'),
            AllowedSort::field('request_id'),
            AllowedSort::field('order_id'),
            AllowedSort::field('order_date'),
            AllowedSort::field('variant_id'),
            AllowedSort::field('variant_name'),
            AllowedSort::field('audit_id'),
            AllowedSort::field('edit_date'),
        ]);
    }

    protected function getDateRangeFilterQuery(array $filters): Closure
    {
        $timeRange = $filters['time_range'] ?? null;

        if ($timeRange && $timeRange != -1) {
            $startDate = now()->subHours($filters['time_range'])->toDateTimeString();
            $endDate = now()->toDateTimeString();
        } else {
            $startDate = Carbon::createFromDate($filters['start_date'])
                ->startOfDay()
                ->toDateTimeString();
            $endDate = Carbon::createFromDate($filters['end_date'])
                ->endOfDay()
                ->toDateTimeString();
        }

        return function ($query) use ($startDate, $endDate) {
            $query->where('audits.created_at', '>=', $startDate)
                ->where('audits.created_at', '<=', $endDate);
        };
    }
}
