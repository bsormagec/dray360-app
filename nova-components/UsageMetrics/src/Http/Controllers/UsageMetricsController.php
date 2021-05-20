<?php

namespace Dray360\UsageMetrics\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Validation\ValidatesRequests;

class UsageMetricsController
{
    use ValidatesRequests;

    protected $companyId;
    protected string $from;
    protected string $previousFrom;
    protected string $previousTo;
    protected string $to;

    public function __invoke(Request $request, $companyId)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->companyId = $companyId;
        $fromDate = Carbon::createFromTimeString($data['start_date'])->startOfDay();
        $toDate = Carbon::createFromTimeString($data['end_date'])->endOfDay();
        $difference = $toDate->diffInDays($fromDate) + 1;

        $from = $fromDate->toDateTimeString();
        $to = $toDate->toDateTimeString();
        $previousFrom = $fromDate->subDays($difference)->toDateTimeString();
        $previousTo = $toDate->subDays($difference)->toDateTimeString();
        return new JsonResource([
            'dates' => [
                'from' => $from,
                'to' => $to,
                'previousFrom' => $previousFrom,
                'previousTo' => $previousTo,
            ],
            'requests' => [
                'current' => $this->getNumberOfRequests($from, $to),
                'previous' => $this->getNumberOfRequests($previousFrom, $previousTo),
            ],
            'pdf_requests' => [
                'current' => $this->getNumberOfPdfRequests($from, $to),
                'previous' => $this->getNumberOfPdfRequests($previousFrom, $previousTo),
            ],
            'datafile_requests' => [
                'current' => $this->getDatafileRequestCount($from, $to),
                'previous' => $this->getDatafileRequestCount($previousFrom, $previousTo),
            ],
            'rejected_requests' => [
                'current' => $this->getRejectedRequestsCount($from, $to),
                'previous' => $this->getRejectedRequestsCount($previousFrom, $previousTo),
            ],
            'orders' => [
                'current' => $this->getOrdersCount($from, $to),
                'previous' => $this->getOrdersCount($previousFrom, $previousTo),
            ],
            'deleted_orders' => [
                'current' => $this->getDeletedOrdersCount($from, $to),
                'previous' => $this->getDeletedOrdersCount($previousFrom, $previousTo),
            ],
            'orders_from_pdf' => [
                'current' => $this->getOrdersFromPdfRequestsCount($from, $to),
                'previous' => $this->getOrdersFromPdfRequestsCount($previousFrom, $previousTo),
            ],
            'orders_from_datafile' => [
                'current' => $this->getOrdersFromDatafilesRequestsCount($from, $to),
                'previous' => $this->getOrdersFromDatafilesRequestsCount($previousFrom, $previousTo),
            ],
            'tms_shipments' => [
                'current' => $this->getTmsShipmentsCreatedCount($from, $to),
                'previous' => $this->getTmsShipmentsCreatedCount($previousFrom, $previousTo),
            ],
            'jpeg_pages' => [
                'current' => $this->getJPEGPagesCount($from, $to),
                'previous' => $this->getJPEGPagesCount($previousFrom, $previousTo),
            ],
            'pdf_pages' => [
                'current' => $this->getPDFPagesCount($from, $to),
                'previous' => $this->getPDFPagesCount($previousFrom, $previousTo),
            ],
        ]);
    }

    protected function getNumberOfRequests($from, $to)
    {
        $response = DB::table('t_job_state_changes')
            ->selectRaw('count(distinct request_id) as request_count')
            ->where('company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->request_count;
    }

    protected function getNumberOfPdfRequests($from, $to)
    {
        $response = DB::table('t_job_state_changes')
            ->selectRaw('count(distinct request_id) as pdf_request_count')
            ->where(DB::raw("json_extract(status_metadata, '$.document_type')"), 'pdf')
            ->where('company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->pdf_request_count;
    }

    protected function getDatafileRequestCount($from, $to)
    {
        $response = DB::table('t_job_state_changes')
            ->selectRaw('count(distinct request_id) as datafile_request_count')
            ->where(DB::raw("json_extract(status_metadata, '$.document_type')"), 'datafile')
            ->where('company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->datafile_request_count;
    }

    protected function getRejectedRequestsCount($from, $to)
    {
        $response = DB::table('t_job_state_changes')
            ->selectRaw('count(distinct request_id) as rejected_request_count')
            ->where('status', OCRRequestStatus::INTAKE_REJECTED)
            ->where('company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->rejected_request_count;
    }

    protected function getOrdersCount($from, $to)
    {
        $response = DB::table('t_orders')
            ->selectRaw('count(distinct id) as order_count')
            ->where('t_company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->order_count;
    }

    protected function getDeletedOrdersCount($from, $to)
    {
        $response = DB::table('t_orders')
            ->selectRaw('count(distinct id) as order_count')
            ->where('t_company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->whereNotNull('deleted_at')
            ->first();

        return $response->order_count;
    }

    protected function getOrdersFromPdfRequestsCount($from, $to)
    {
        $response = DB::table('t_orders', 'o')
            ->selectRaw('count(distinct o.id) as pdf_order_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('o.request_id', '=', 's.request_id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED);
            })
            ->where('o.t_company_id', $this->companyId)
            ->where('o.created_at', '>=', $from)
            ->where('o.created_at', '<=', $to)
            ->first();

        return $response->pdf_order_count;
    }

    protected function getOrdersFromDatafilesRequestsCount($from, $to)
    {
        $response = DB::table('t_orders', 'o')
            ->selectRaw('count(distinct o.id) as datafile_order_count')
            ->join('t_job_state_changes as s', function ($join) {
                $join->on('o.request_id', '=', 's.request_id')
                    ->where('s.status', OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE);
            })
            ->where('o.t_company_id', $this->companyId)
            ->where('o.created_at', '>=', $from)
            ->where('o.created_at', '<=', $to)
            ->first();

        return $response->datafile_order_count;
    }

    protected function getTmsShipmentsCreatedCount($from, $to)
    {
        $response = DB::table('t_orders')
            ->selectRaw('count(distinct tms_shipment_id) as tms_shipment_count')
            ->where('t_company_id', $this->companyId)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->first();

        return $response->tms_shipment_count;
    }

    protected function getPDFPagesCount($from, $to)
    {
        $response = DB::selectOne("
        select sum(json_extract(status_metadata, '$.pdf_page_count')) as pdf_page_count
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

    protected function getJPEGPagesCount($from, $to)
    {
        $response = DB::selectOne("
            select coalesce(sum(request_page_count), 0) as jpg_page_count
            from (
                select
                    request_id
                    ,group_concat(distinct order_id order by order_id) as request_order_list
                    ,count(distinct order_id) as request_order_count
                    ,max(page_index_filenames_length) as request_page_count
                from (
                    select
                        o.request_id
                        ,o.id as order_id
                        ,json_extract(o.ocr_data, '$.page_index_filenames.value') as page_index_filenames
                        ,json_length (o.ocr_data, '$.page_index_filenames.value') as page_index_filenames_length
                    from t_orders as o
                    where o.t_company_id = ?
                    and date(created_at) >= ?
                    and date(created_at) <= ?
                    having page_index_filenames like '%jpg%'
                ) as subq
                group by request_id
                ) as outerq
        ", [$this->companyId, $from, $to]);

        return intval($response->jpg_page_count);
    }
}
