<?php

namespace App\Queries;

use Closure;
use App\Models\Order;
use App\Models\Company;
use App\Models\OrderLineItem;
use Illuminate\Support\Carbon;
use App\Models\OrderAddressEvent;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AuditLogsDashboardQuery extends QueryBuilder
{
    public function __construct(array $filters)
    {
        $auditsFilterQuery = $this->getAuditsFilterQuery($filters);
        $tcompaniesDemoId = Company::TCOMPANIES_DEMO_ID;

        $query = Order::query()
            ->select([
                't_orders.id',
                't_orders.request_id',
                't_orders.variant_name',
                't_orders.t_company_id',
                't_orders.created_at',
                't_orders.updated_at',
                DB::raw("json_extract(ocr_data, '$.fields.last_editor.value') as verifier"),
                DB::raw('if(t_orders.variant_id = -1 or t_orders.variant_id is null, t_orders.variant_name, t_orders.variant_id) as variant_id')
            ])
            ->addSelect([
                'changes_count' => DB::raw("
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    where audits.auditable_type = '". str_replace('\\', '\\\\', Order::class) ."'
                    and audits.auditable_id = t_orders.id
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    join t_order_line_items on audits.auditable_id = t_order_line_items.id
                        and t_order_line_items.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderLineItem::class) ."'
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ),0)
                    from audits
                    join t_order_address_events on audits.auditable_id = t_order_address_events.id
                        and t_order_address_events.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderAddressEvent::class) ."'
                    ) as changes_count
                "),
                't_companies_changes_count' => DB::raw("
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    join users on users.id = user_id and t_company_id = {$tcompaniesDemoId}
                    where audits.auditable_type = '". str_replace('\\', '\\\\', Order::class) ."'
                    and audits.auditable_id = t_orders.id
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    join users on users.id = user_id and t_company_id = {$tcompaniesDemoId}
                    join t_order_line_items on audits.auditable_id = t_order_line_items.id
                        and t_order_line_items.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderLineItem::class) ."'
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ),0)
                    from audits
                    join users on users.id = user_id and t_company_id = {$tcompaniesDemoId}
                    join t_order_address_events on audits.auditable_id = t_order_address_events.id
                        and t_order_address_events.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderAddressEvent::class) ."'
                    ) as t_companies_changes_count
                "),
                'client_changes_count' => DB::raw("
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    join users on users.id = user_id and t_company_id != {$tcompaniesDemoId}
                    where audits.auditable_type = '". str_replace('\\', '\\\\', Order::class) ."'
                    and audits.auditable_id = t_orders.id
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ), 0)
                    from audits
                    join users on users.id = user_id and t_company_id != {$tcompaniesDemoId}
                    join t_order_line_items on audits.auditable_id = t_order_line_items.id
                        and t_order_line_items.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderLineItem::class) ."'
                    )
                    +
                    (select coalesce(sum(
                        if(json_length(old_values) = 0, json_length(new_values), json_length(old_values))
                    ),0)
                    from audits
                    join users on users.id = user_id and t_company_id != {$tcompaniesDemoId}
                    join t_order_address_events on audits.auditable_id = t_order_address_events.id
                        and t_order_address_events.t_order_id = t_orders.id
                    where audits.auditable_type = '". str_replace('\\', '\\\\', OrderAddressEvent::class) ."'
                    ) as client_changes_count
                "),
            ])
            ->with([
                'audits.user',
                'company:id,name',
            ])
            ->with('orderAddressEvents', function ($query) {
                $query
                    ->select(['id', 't_order_id'])
                    ->withTrashed()
                    ->with('audits.user');
            })
            ->with('orderLineItems', function ($query) {
                $query
                    ->select(['id', 't_order_id'])
                    ->withTrashed()
                    ->with('audits.user');
            })
            ->where($this->getDateRangeFilterQuery($filters))
            ->where(function ($where) use ($auditsFilterQuery) {
                $where->orWhereHas('audits', $auditsFilterQuery)
                    ->orWhereHas('orderAddressEvents.audits', $auditsFilterQuery)
                    ->orWhereHas('orderLineItems.audits', $auditsFilterQuery);
            })
            ->whereDoesntHave('audits', function ($query) {
                $query->where('event', 'created');
            })
            ->join('t_companies as c', 'c.id', '=', 't_orders.t_company_id');

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::partial('variant_name', 't_orders.variant_name'),
            AllowedFilter::exact('variant_id'),
            AllowedFilter::exact('company_id', 't_orders.t_company_id', false),
        ])
        ->defaultSort('-t_orders.id')
        ->allowedSorts([
            AllowedSort::field('id', 't_orders.id'),
            AllowedSort::field('request_id', 't_orders.request_id'),
            AllowedSort::field('company.name', 'c.name'),
            AllowedSort::field('variant_name', 't_orders.variant_name'),
            AllowedSort::field('variant_id', 't_orders.variant_id'),
            AllowedSort::field('created_at', 't_orders.created_at'),
            AllowedSort::field('updated_at', 't_orders.updated_at'),
            AllowedSort::field('changes_count'),
            AllowedSort::field('client_changes_count'),
            AllowedSort::field('t_companies_changes_count'),
            AllowedSort::field('verifier'),
        ])
        ;
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
            $query->where('t_orders.created_at', '>=', $startDate)
                ->where('t_orders.created_at', '<=', $endDate);
        };
    }

    protected function getAuditsFilterQuery(array $filters): Closure
    {
        $userId = isset($filters['user_id']) ? explode(',', $filters['user_id']) : null;

        return fn ($query) => $query->when($userId, fn ($q) => $q->whereIn('user_id', $userId));
    }
}
