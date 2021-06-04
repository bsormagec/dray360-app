<?php

namespace App\Queries\Metrics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class RequestsOrdersSharedMetricsQuery
{
    protected Carbon $date;
    protected int $companyId;

    public function __invoke(Carbon $date, int $companyId)
    {
        $this->date = $date;
        $this->companyId = $companyId;

        $baseData = $this->getBaseData();

        return [
            'requests' => $baseData->count(), // G
            'requests_all_updateprior' => $baseData->sum('all_updateprior'), // E
            'requests_mixed_updateprior' => $baseData->sum('mixed_updateprior'),
            'requests_none_updateprior' => $baseData->sum('none_updateprior'),

            'pdf_requests' => $baseData->sum('is_pdf'),
            'pdf_requests_all_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->all_updateprior),
            'pdf_requests_mixed_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->mixed_updateprior),
            'pdf_requests_none_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->none_updateprior),

            'pdf_requests_singleorder' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_singleorder),
            'pdf_requests_singleorder_all_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_singleorder && $item->all_updateprior),
            'pdf_requests_singleorder_mixed_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_singleorder && $item->mixed_updateprior),
            'pdf_requests_singleorder_none_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_singleorder && $item->none_updateprior), // A

            'pdf_requests_multiorder' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder),
            'pdf_requests_multiorder_all_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder && $item->all_updateprior),
            'pdf_requests_multiorder_mixed_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder && $item->mixed_updateprior),
            'pdf_requests_multiorder_none_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder && $item->none_updateprior),
            'pdf_requests_multiorder_less_all_updateprior' =>
                $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder)
                - $baseData->sum(fn ($item) => $item->is_pdf && $item->is_multiorder && $item->all_updateprior), // B


            'pdf_orders' => $baseData->sum(fn ($item) => $item->is_pdf * $item->orders),
            'pdf_orders_updateprior' => $baseData->sum(fn ($item) => $item->is_pdf * $item->orders_updateprior), // J
            'pdf_orders_dontupdateprior' => $baseData->sum(fn ($item) => $item->is_pdf * $item->orders_dontupdateprior),
            'pdf_orders_less_requests_anyupdateprior' =>
                $baseData->sum(fn ($item) => $item->is_pdf * $item->orders_dontupdateprior)
                - $baseData->sum(fn ($item) => $item->is_pdf && ! $item->all_updateprior), // H

            'datafile_requests' => $baseData->sum('is_datafile'),

            'datafile_requests_all_updateprior' => $baseData->sum(fn ($item) => $item->is_datafile && $item->all_updateprior),
            'datafile_requests_mixed_updateprior' => $baseData->sum(fn ($item) => $item->is_datafile && $item->mixed_updateprior),
            'datafile_requests_none_updateprior' => $baseData->sum(fn ($item) => $item->is_datafile && $item->none_updateprior), // D

            'datafile_orders' => $baseData->sum(fn ($item) => $item->is_datafile * $item->orders),
            'datafile_orders_updateprior' => $baseData->sum(fn ($item) => $item->is_datafile * $item->orders_updateprior), // K
            'datafile_orders_dontupdateprior' => $baseData->sum(fn ($item) => $item->is_datafile * $item->orders_dontupdateprior),
            'datafile_orders_less_requests_anyupdateprior' =>
                $baseData->sum(fn ($item) => $item->is_datafile * $item->orders_dontupdateprior)
                - $baseData->sum(fn ($item) => $item->is_datafile && ! $item->all_updateprior), // I
        ];
    }

    protected function getBaseData(): Collection
    {
        return DB::table('t_orders ', 'o')
            ->select(['o.request_id'])
            ->addSelect(DB::raw("sum(1) as orders"))
            ->addSelect(DB::raw("sum(preceded_by_order_id is not null) as orders_updateprior"))
            ->addSelect(DB::raw("sum(preceded_by_order_id is null) as orders_dontupdateprior"))
            ->addSelect(DB::raw("(select json_extract(s.status_metadata, '$.document_type') from t_job_state_changes as s where s.status in ('intake-accepted', 'intake-accepted-datafile') and o.request_id = s.request_id order by id asc limit 1) as document_type"))
            ->addSelect(DB::raw("(select json_extract(s.status_metadata, '$.document_type') from t_job_state_changes as s where s.status in ('intake-accepted', 'intake-accepted-datafile') and o.request_id = s.request_id order by id asc limit 1) = 'pdf' as is_pdf"))
            ->addSelect(DB::raw("(select json_extract(s.status_metadata, '$.document_type') from t_job_state_changes as s where s.status in ('intake-accepted', 'intake-accepted-datafile') and o.request_id = s.request_id order by id asc limit 1) = 'datafile' as is_datafile"))
            ->addSelect(DB::raw("(sum(1) = 1) as is_singleorder"))
            ->addSelect(DB::raw("(sum(1) > 1) as is_multiorder"))
            ->addSelect(DB::raw("(sum(preceded_by_order_id is null) = 0) as all_updateprior"))
            ->addSelect(DB::raw("(sum(preceded_by_order_id is not null) > 0 and sum(preceded_by_order_id is null) > 0) as mixed_updateprior"))
            ->addSelect(DB::raw("(sum(preceded_by_order_id is not null) = 0) as none_updateprior"))
            ->addSelect(DB::raw("(sum(preceded_by_order_id is not null) > 0) as any_updateprior"))
            ->whereNotExists(function (Builder $query) {
                $query->select('id')
                    ->from('t_job_latest_state', 'l')
                    ->whereColumn('l.request_id', 'o.request_id')
                    ->whereNotNull('l.deleted_at');
            })
            ->whereNull('o.deleted_at')
            ->whereDate('o.created_at', '>=', $this->date)
            ->whereDate('o.created_at', '<=', $this->date)
            ->where('o.t_company_id', $this->companyId)
            ->groupBy('o.request_id')
            ->get();
    }
}
