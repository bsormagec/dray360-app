<?php

namespace App\Queries;

use App\Models\Order;
use App\Models\OCRRequestStatus;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Queries\Filters\OrderStatusFilter;
use App\Queries\Filters\CreatedBetweenFilter;

class OrdersListQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = Order::query()
            ->select([
                't_orders.id',
                't_orders.request_id',
                't_orders.created_at',
                't_orders.updated_at',
                't_orders.equipment_type_raw_text',
                't_orders.shipment_designation',
                't_orders.shipment_direction',
                't_orders.tms_shipment_id',
                't_orders.t_company_id',
                't_orders.tms_template_dictid',
                't_orders.bill_to_address_id',
                't_orders.unit_number',
                't_orders.reference_number',
                't_orders.is_hidden',
                't_orders.preceded_by_order_id',
                't_orders.variant_name',
            ])
            ->addSelect(['request_is_hidden' => DB::table('t_job_latest_state', 's_s')
                ->selectRaw("count(*) as request_is_hidden")
                ->whereColumn('s_s.request_id', 't_orders.request_id')
                ->whereNull('s_s.order_id')
                ->whereNotNull('s_s.done_at')
                ->groupBy('s_s.request_id')
                ->limit(1)
            ])
            ->addSelect(['company' => DB::table('t_companies', 'c')
                ->select(['c.name'])
                ->whereColumn('c.id', 't_orders.t_company_id')
                ->limit(1)
            ])
            ->addSelect(['bill_to_address_name' => DB::table('t_addresses', 'bill_to')
                ->select(['bill_to.location_name'])
                ->whereColumn('bill_to.id', 't_orders.bill_to_address_id')
                ->limit(1)
            ])
            ->join('t_job_latest_state as ls_sort', 'ls_sort.order_id', '=', 't_orders.id')
            ->join('t_job_state_changes as s_sort', 's_sort.id', '=', 'ls_sort.t_job_state_changes_id')
            ->when(! auth()->user()->isAbleTo('all-companies-view') && currentCompany(), function ($query) {
                return $query->where('t_orders.t_company_id', '=', currentCompany()->id);
            })
            ->when(! auth()->user()->isAbleTo('admin-review-view'), function ($query) {
                return $query->whereDoesntHave('ocrRequest.latestOcrRequestStatus', function ($query) {
                    $query->where('status', OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_REVIEW);
                });
            })
            ->with([
                'ocrRequest:order_id,created_at,updated_at,t_job_state_changes_id',
                'ocrRequest.latestOcrRequestStatus:id,status,status_metadata',
                'locks',
                'tmsTemplate:id,item_key,item_display_name',
            ]);

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::partial('request_id', 't_orders.request_id'),
            AllowedFilter::exact('company_id', 't_orders.t_company_id', false),
            AllowedFilter::custom('created_between', new CreatedBetweenFilter(), 't_orders.created_at'),
            AllowedFilter::custom('status', new OrderStatusFilter()),
            AllowedFilter::custom('display_status', new OrderStatusFilter()),
            AllowedFilter::callback('query', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $query->orWhere('t_orders.request_id', 'like', "{$value}%")
                        ->orWhere('t_orders.id', '=', $value)
                        ->orWhere('t_orders.equipment_type_raw_text', 'like', "{$value}%")
                        ->orWhere('t_orders.shipment_designation', 'like', "{$value}%")
                        ->orWhere('t_orders.shipment_direction', 'like', "{$value}%")
                        ->orWhere('t_orders.tms_shipment_id', 'like', "%{$value}%")
                        ->orWhere('t_orders.unit_number', 'like', "%{$value}%")
                        ->orWhere('t_orders.reference_number', 'like', "%{$value}%")
                        ->orWhereHas('billToAddress', fn ($q) => $q->where('location_name', 'like', "%{$value}%"));
                });
            }),
            AllowedFilter::callback('hidden', function ($query, $value) {
                if (! $value) {
                    $query->where('is_hidden', false);
                }
            })->default(false)
        ])
        ->defaultSort('-t_orders.created_at', '-t_orders.id')
        ->allowedSorts([
            AllowedSort::field('id', 't_orders.id'),
            AllowedSort::field('tms_shipment_id', 't_orders.tms_shipment_id'),
            AllowedSort::field('request_id', 't_orders.request_id'),
            AllowedSort::field('created_at', 't_orders.created_at'),
            AllowedSort::field('updated_at', 't_orders.updated_at'),
            AllowedSort::field('reference_number', 't_orders.reference_number'),
            AllowedSort::field('unit_number', 't_orders.unit_number'),
            AllowedSort::field('status', 's_sort.status'),
            AllowedSort::field('order.equipment_type_raw_text', 't_orders.equipment_type_raw_text'),
            AllowedSort::field('order.shipment_designation', 't_orders.shipment_designation'),
            AllowedSort::field('order.shipment_direction', 't_orders.shipment_direction'),
            AllowedSort::field('order.bill_to_address', 'bill_to_address_name'),
            AllowedSort::field('company', 'company'),
        ]);
    }
}
