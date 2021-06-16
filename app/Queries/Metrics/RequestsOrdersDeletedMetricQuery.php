<?php

namespace App\Queries\Metrics;

use Illuminate\Support\Carbon;
use App\Models\OCRRequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class RequestsOrdersDeletedMetricQuery
{
    protected Carbon $date;
    protected int $companyId;

    public function __invoke(Carbon $date, int $companyId)
    {
        $this->date = $date;
        $this->companyId = $companyId;

        $data = [
            'orders' => $this->orders(false),
            'orders_deleted' => $this->orders(true),
            'pdf_orders_deleted' => $this->pdfOrders(true),
            'pdf_orders_including_deleted' => $this->pdfOrders(false),
            'datafile_orders_deleted' => $this->datafileOrders(true),
            'datafile_orders_including_deleted' => $this->datafileOrders(false),
            'requests_rejected' => $this->requestsRejected(), // F
            'pdf_pages_including_deleted' => $this->pdfPages(),
            'tms_shipments' => $this->tmsShipments(), // M
            'requests_deleted' => $this->requestsDeleted(), // N
            'pdf_requests_deleted' => $this->pdfRequestsDeleted(),
            'datafile_requests_deleted' => $this->datafileRequestsDeleted(),
        ];

        $data['pdf_pages_overage'] = max(
            0,
            $data['pdf_pages_including_deleted'] - (2 * $data['pdf_orders_including_deleted'])
        ); // L

        return $data;
    }

    protected function baseOrdersQuery(bool $onlyDeleted): Builder
    {
        return DB::table('t_orders', 'o')
            ->where('o.t_company_id', $this->companyId)
            ->whereDate('o.created_at', '>=', $this->date)
            ->whereDate('o.created_at', '<=', $this->date)
            ->when($onlyDeleted, function (Builder $query) {
                return $query->whereNotNull('o.deleted_at');
            });
    }

    protected function orders(bool $onlyDeleted): int
    {
        $response = $this->baseOrdersQuery($onlyDeleted)
            ->selectRaw('count(distinct o.id) as order_count')
            ->first();

        return $response->order_count;
    }

    protected function pdfOrders(bool $onlyDeleted): int
    {
        $response = $this->baseOrdersQuery($onlyDeleted)
            ->selectRaw('count(distinct o.id) as pdf_order_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('o.request_id', '=', 's.request_id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED);
            })
            ->first();

        return $response->pdf_order_count;
    }

    protected function datafileOrders(bool $onlyDeleted): int
    {
        $response = $this->baseOrdersQuery($onlyDeleted)
            ->selectRaw('count(distinct o.id) as datafile_order_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('o.request_id', '=', 's.request_id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE);
            })
            ->first();

        return $response->datafile_order_count;
    }

    protected function requestsRejected(): int
    {
        $response = DB::table('t_job_state_changes')
            ->selectRaw('count(distinct request_id) as rejected_request_count')
            ->where('status', OCRRequestStatus::INTAKE_REJECTED)
            ->where('company_id', $this->companyId)
            ->whereDate('created_at', '>=', $this->date)
            ->whereDate('created_at', '<=', $this->date)
            ->first();

        return $response->rejected_request_count;
    }

    protected function pdfPages(): int
    {
        $from = $this->date->clone()->startOfDay()->toDateTimeString();
        $to = $this->date->clone()->endOfDay()->toDateTimeString();

        $response = DB::selectOne("
        select sum(coalesce(json_extract(status_metadata, '$.pdf_page_count'), 0)) as pdf_page_count
            from t_job_state_changes as s
            join (
                select max(id) as max_id
                from t_job_state_changes
                where status = 'intake-started'
                  and company_id = ?
                  and created_at >= ?
                  and created_at <= ?
                group by request_id
            ) as latest_request on s.id = latest_request.max_id
        ", [$this->companyId, $from, $to]);

        return intval($response->pdf_page_count);
    }

    protected function tmsShipments(): int
    {
        $response = $this->baseOrdersQuery(false)
            ->selectRaw('count(distinct o.tms_shipment_id) as tms_shipment_count')
            ->first();

        return $response->tms_shipment_count;
    }

    protected function requestsDeleted(): int
    {
        $response = DB::table('t_job_latest_state', 'ls')
            ->selectRaw('count(distinct ls.request_id) as requests_count')
            ->join('t_job_state_changes as sc', 'sc.id', '=', 'ls.t_job_state_changes_id')
            ->where('sc.company_id', $this->companyId)
            ->whereDate('ls.created_at', '>=', $this->date)
            ->whereDate('ls.created_at', '<=', $this->date)
            ->whereNotNull('ls.deleted_at')
            ->first();

        return $response->requests_count;
    }

    protected function pdfRequestsDeleted(): int
    {
        $response = DB::table('t_job_latest_state', 'ls')
            ->selectRaw('count(distinct ls.id) as pdf_requests_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('ls.t_job_state_changes_id', '=', 's.id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED);
            })
            ->where('s.company_id', $this->companyId)
            ->whereDate('ls.created_at', '>=', $this->date)
            ->whereDate('ls.created_at', '<=', $this->date)
            ->whereNotNull('ls.deleted_at')
            ->first();

        return $response->pdf_requests_count;
    }

    protected function datafileRequestsDeleted(): int
    {
        $response = DB::table('t_job_latest_state', 'ls')
            ->selectRaw('count(distinct ls.id) as pdf_requests_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('ls.t_job_state_changes_id', '=', 's.id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE);
            })
            ->where('s.company_id', $this->companyId)
            ->whereDate('ls.created_at', '>=', $this->date)
            ->whereDate('ls.created_at', '<=', $this->date)
            ->whereNotNull('ls.deleted_at')
            ->first();

        return $response->pdf_requests_count;
    }
}
