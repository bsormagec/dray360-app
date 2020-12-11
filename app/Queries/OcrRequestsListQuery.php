<?php

namespace App\Queries;

use App\Models\OCRRequest;
use App\Models\OCRRequestStatus;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Queries\Sorts\OcrRequestStatusSort;
use App\Queries\Filters\CreatedBetweenFilter;
use App\Queries\Filters\OcrRequestStatusFilter;

class OcrRequestsListQuery extends QueryBuilder
{
    public function __construct($requestId = null)
    {
        $query = OCRRequest::query()
                ->select([
                    't_job_latest_state.*',
                    'c.name as company_name',
                    'c.id as company_id',
                    DB::raw('(select min(o2.id) from t_orders as o2 where o2.request_id = s.request_id and o2.deleted_at is null) as first_order_id')
                ])
                ->addSelect(['email_from_address' => DB::table('t_job_state_changes', 's_is')
                    ->selectRaw("json_extract(s_is.status_metadata, '$.source_summary.source_email_from_address') as email_from_address")
                    ->whereColumn('t_job_latest_state.request_id', 's_is.request_id')
                    ->where('s_is.status', OCRRequestStatus::INTAKE_STARTED)
                    ->limit(1)
                ])
                ->addSelect(['upload_user_name' => DB::table('t_job_state_changes', 's_ur')
                    ->select('u.name')
                    ->whereColumn('t_job_latest_state.request_id', 's_ur.request_id')
                    ->where('s_ur.status', OCRRequestStatus::UPLOAD_REQUESTED)
                    ->join('users as u', DB::raw("json_extract(s_ur.status_metadata, '$.user_id')"), '=', 'u.id')
                    ->limit(1)
                ])
                ->addSelect(['first_order_bill_to_address_location_name' => DB::table('t_addresses', 'a')
                    ->select('a.location_name')
                    ->join('t_orders as o', function ($join) {
                        $join->on('o.bill_to_address_id', '=', 'a.id');
                    })
                    ->whereColumn('o.request_id', 't_job_latest_state.request_id')
                    ->orderBy('o.id')
                    ->limit(1)
                ])
                ->addSelect(['tms_template_id' => DB::table('t_orders', 'o')
                    ->select('o.tms_template_id')
                    ->whereColumn('o.request_id', 't_job_latest_state.request_id')
                    ->whereNotNull('tms_template_id')
                    ->orderBy('o.id')
                    ->limit(1)
                ])
                ->join('t_job_state_changes as s', 't_job_latest_state.t_job_state_changes_id', '=', 's.id')
                ->join('t_companies as c', 's.company_id', '=', 'c.id')
                ->when(! is_superadmin() && currentCompany(), function ($query) {
                    return $query->where('s.company_id', '=', currentCompany()->id);
                })
                ->whereNull('t_job_latest_state.order_id')
                ->withCount('orders')
                ->with([
                    'latestOcrRequestStatus:id,status,status_date,status_metadata',
                ])
                ->when($requestId, function ($query) use ($requestId) {
                    return $query->orderByDesc(DB::raw("\"{$requestId}\" = t_job_latest_state.request_id"));
                });

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::partial('request_id', 't_job_latest_state.request_id'),
            AllowedFilter::custom('created_between', new CreatedBetweenFilter(), 't_job_latest_state.created_at'),
            AllowedFilter::custom('status', new OcrRequestStatusFilter()),
            AllowedFilter::custom('display_status', new OcrRequestStatusFilter()),
            AllowedFilter::callback('query', function ($query, $value) {
                $query
                // ->where(function ($query) use ($value) {
                //     $query->orWhere('t_job_latest_state.request_id', 'like', "%{$value}%")
                //         ->orWhereRaw('1=1');
                // })
                ->orHaving('first_order_bill_to_address_location_name', 'like', "%{$value}%")
                ->orHaving('t_job_latest_state.request_id', 'like', "%{$value}%");
            }),
        ])
        ->defaultSort('-t_job_latest_state.created_at')
        ->allowedSorts([
            AllowedSort::field('id', 't_job_latest_state.id'),
            AllowedSort::field('request_id', 't_job_latest_state.request_id'),
            AllowedSort::field('created_at', 't_job_latest_state.created_at'),
            AllowedSort::custom('status', new OcrRequestStatusSort()),
        ]);
    }
}
