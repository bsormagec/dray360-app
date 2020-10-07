<?php

namespace App\Queries;

use App\Models\OCRRequest;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Queries\Sorts\OcrRequestStatusSort;
use App\Queries\Filters\CreatedBetweenFilter;
use App\Queries\Filters\OcrRequestStatusFilter;

class OcrRequestOrderListQuery extends QueryBuilder
{
    public function __construct()
    {
        $noDuplicateOrderRequestsWhereClause = <<<ENDOFSQL
            (t_job_latest_state.order_id is not null or
            (t_job_latest_state.order_id is null and t_job_latest_state.request_id not in (
                select l2.request_id from t_job_latest_state as l2 where l2.order_id is not null
            )))
        ENDOFSQL;

        $query = OCRRequest::query()
            ->select('t_job_latest_state.*')
            ->addSelect('t_orders.id as t_order_id')
            ->when(! is_superadmin() && currentCompany(), function ($query) {
                return $query->join('t_job_state_changes', function ($join) {
                    $join->on('t_job_latest_state.t_job_state_changes_id', '=', 't_job_state_changes.id')
                    ->where('t_job_state_changes.company_id', '=', currentCompany()->id);
                });
            })
            ->with([
                'order:id,request_id,bill_to_address_raw_text,created_at,equipment_type,shipment_designation,shipment_direction,tms_shipment_id,bill_to_address_id,unit_number,reference_number',
                'order.billToAddress',
                'latestOcrRequestStatus:id,status,status_date',
            ])
            ->whereRaw($noDuplicateOrderRequestsWhereClause);

        parent::__construct($query);

        if (! $this->request->hasAny(['filter.status', 'filter.display_status'])) {
            $this->leftJoin('t_orders', 't_orders.request_id', '=', 't_job_latest_state.request_id');
        }

        $this->allowedFilters([
            AllowedFilter::partial('request_id', 't_job_latest_state.request_id'),
            AllowedFilter::partial('order.bill_to_address_raw_text', 't_orders.bill_to_address_raw_text', false),
            AllowedFilter::partial('order.port_ramp_of_origin_address_raw_text', 't_orders.port_ramp_of_origin_address_raw_text', false),
            AllowedFilter::partial('order.port_ramp_of_destination_address_raw_text', 't_orders.port_ramp_of_destination_address_raw_text', false),
            AllowedFilter::partial('order.equipment_type', 't_orders.equipment_type', false),
            AllowedFilter::partial('order.shipment_designation', 't_orders.shipment_designation', false),
            AllowedFilter::partial('order.shipment_direction', 't_orders.shipment_direction', false),
            AllowedFilter::custom('created_between', new CreatedBetweenFilter(), 't_job_latest_state.created_at'),
            AllowedFilter::custom('status', new OcrRequestStatusFilter(true)),
            AllowedFilter::custom('display_status', new OcrRequestStatusFilter(true)),
            AllowedFilter::callback('query', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $query->orWhere('t_orders.bill_to_address_raw_text', 'like', "%{$value}%")
                    ->orWhere('t_job_latest_state.request_id', 'like', "%{$value}%")
                    ->orWhere('t_orders.port_ramp_of_origin_address_raw_text', 'like', "%{$value}%")
                    ->orWhere('t_orders.port_ramp_of_destination_address_raw_text', 'like', "%{$value}%")
                    ->orWhere('t_orders.equipment_type', 'like', "%{$value}%")
                    ->orWhere('t_orders.shipment_designation', 'like', "%{$value}%")
                    ->orWhere('t_orders.shipment_direction', 'like', "%{$value}%");
                });
            }),
        ])
        ->defaultSort('-t_job_latest_state.created_at', '-t_orders.id')
        ->allowedSorts([
            AllowedSort::field('request_id', 't_job_latest_state.request_id'),
            AllowedSort::field('created_at', 't_job_latest_state.created_at'),
            AllowedSort::custom('status', new OcrRequestStatusSort()),
            AllowedSort::field('order.bill_to_address_raw_text', 't_orders.bill_to_address_raw_text'),
            AllowedSort::field('order.equipment_type', 't_orders.equipment_type'),
            AllowedSort::field('order.shipment_designation', 't_orders.shipment_designation'),
            AllowedSort::field('order.shipment_direction', 't_orders.shipment_direction'),
        ]);
    }
}
