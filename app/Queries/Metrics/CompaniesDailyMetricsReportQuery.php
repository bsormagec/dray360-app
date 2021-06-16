<?php

namespace App\Queries\Metrics;

use App\Models\CompanyDailyMetric;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Collection;

class CompaniesDailyMetricsReportQuery extends QueryBuilder
{
    const QUERY_KEY = 'company-daily-report';

    public function __construct(array $filters)
    {
        $query = CompanyDailyMetric::query()
            ->select([
                'c.name as company_name',
                DB::raw("sum(pdf_requests_singleorder_none_updateprior) as A_pdf_requests_singleorder_none_updateprior"),
                DB::raw("sum(pdf_requests_multiorder_less_all_updateprior) as B_pdf_requests_multiorder_less_all_updateprior"),
                DB::raw("sum(datafile_requests_none_updateprior) as D_datafile_requests_none_updateprior"),
                DB::raw("sum(requests_all_updateprior) as E_requests_all_updateprior"),
                DB::raw("sum(requests_rejected) as F_requests_rejected"),
                DB::raw("sum(requests) as G_requests"),
                DB::raw("sum(pdf_orders_less_requests_anyupdateprior) as H_pdf_orders_less_requests_anyupdateprior"),
                DB::raw("sum(datafile_orders_less_requests_anyupdateprior) as I_datafile_orders_less_requests_anyupdateprior"),
                DB::raw("sum(pdf_orders_updateprior) as J_pdf_orders_updateprior"),
                DB::raw("sum(datafile_orders_updateprior) as K_datafile_orders_updateprior"),
                DB::raw("sum(pdf_pages_overage) as L_pdf_pages_overages"),
                DB::raw("sum(tms_shipments) as M_tms_shipments"),
                DB::raw("sum(requests_deleted) as N_requests_deleted"),
                DB::raw("sum(pdf_orders_including_deleted) as O_pdf_orders_including_deleted"),
                DB::raw("sum(datafile_orders_including_deleted) as P_datafile_orders_including_deleted"),
                DB::raw("sum(pdf_pages_including_deleted) as Q_pdf_pages_including_deleted"),
                DB::raw("sum(requests_mixed_updateprior) as requests_mixed_updateprior"),
                DB::raw("sum(requests_none_updateprior) as requests_none_updateprior"),
                DB::raw("sum(pdf_requests) as pdf_requests"),
                DB::raw("sum(pdf_requests_all_updateprior) as pdf_requests_all_updateprior"),
                DB::raw("sum(pdf_requests_mixed_updateprior) as pdf_requests_mixed_updateprior"),
                DB::raw("sum(pdf_requests_none_updateprior) as pdf_requests_none_updateprior"),
                DB::raw("sum(pdf_requests_singleorder) as pdf_requests_singleorder"),
                DB::raw("sum(pdf_requests_singleorder_all_updateprior) as pdf_requests_singleorder_all_updateprior"),
                DB::raw("sum(pdf_requests_singleorder_mixed_updateprior) as pdf_requests_singleorder_mixed_updateprior"),
                DB::raw("sum(pdf_requests_multiorder) as pdf_requests_multiorder"),
                DB::raw("sum(pdf_requests_multiorder_all_updateprior) as pdf_requests_multiorder_all_updateprior"),
                DB::raw("sum(pdf_requests_multiorder_mixed_updateprior) as pdf_requests_multiorder_mixed_updateprior"),
                DB::raw("sum(pdf_requests_multiorder_none_updateprior) as pdf_requests_multiorder_none_updateprior"),
                DB::raw("sum(pdf_orders) as pdf_orders"),
                DB::raw("sum(pdf_orders_dontupdateprior) as pdf_orders_dontupdateprior"),
                DB::raw("sum(datafile_requests) as datafile_requests"),
                DB::raw("sum(datafile_requests_all_updateprior) as datafile_requests_all_updateprior"),
                DB::raw("sum(datafile_requests_mixed_updateprior) as datafile_requests_mixed_updateprior"),
                DB::raw("sum(datafile_orders) as datafile_orders"),
                DB::raw("sum(datafile_orders_dontupdateprior) as datafile_orders_dontupdateprior"),
                DB::raw("sum(orders_deleted) as orders_deleted"),
                DB::raw("sum(pdf_orders_deleted) as pdf_orders_deleted"),
                DB::raw("sum(datafile_orders_deleted) as datafile_orders_deleted"),
                DB::raw("sum(pdf_requests_deleted) as pdf_requests_deleted"),
                DB::raw("sum(datafile_requests_deleted) as datafile_requests_deleted"),
                DB::raw("sum(orders) as orders"),
            ])
            ->whereDate('metric_date', '>=', $filters['start_date'])
            ->whereDate('metric_date', '<=', $filters['end_date'])
            ->where([
                ['c.name', 'not like', '%onboarding%'],
                ['c.name', 'not like', '%demo%'],
            ])
            ->havingRaw('(G_requests + N_requests_deleted + orders + orders_deleted) > 0')
            ->join('t_companies as c', 'c.id', '=', 't_company_id')
            ->groupBy('t_company_id');

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::exact('company_id', 't_company_id', false),
        ])
        ->defaultSort('company_name')
        ->allowedSorts([
            'company_name',
            'A_pdf_requests_singleorder_none_updateprior',
            'B_pdf_requests_multiorder_less_all_updateprior',
            'D_datafile_requests_none_updateprior',
            'E_requests_all_updateprior',
            'F_requests_rejected',
            'G_requests',
            'H_pdf_orders_less_requests_anyupdateprior',
            'I_datafile_orders_less_requests_anyupdateprior',
            'J_pdf_orders_updateprior',
            'K_datafile_orders_updateprior',
            'L_pdf_pages_overages',
            'M_tms_shipments',
            'N_requests_deleted',
            'O_pdf_orders_including_deleted',
            'P_datafile_orders_including_deleted',
            'Q_pdf_pages_including_deleted',
            'requests_mixed_updateprior',
            'requests_none_updateprior',
            'pdf_requests',
            'pdf_requests_all_updateprior',
            'pdf_requests_mixed_updateprior',
            'pdf_requests_none_updateprior',
            'pdf_requests_singleorder',
            'pdf_requests_singleorder_all_updateprior',
            'pdf_requests_singleorder_mixed_updateprior',
            'pdf_requests_multiorder',
            'pdf_requests_multiorder_all_updateprior',
            'pdf_requests_multiorder_mixed_updateprior',
            'pdf_requests_multiorder_none_updateprior',
            'pdf_orders',
            'pdf_orders_dontupdateprior',
            'datafile_requests',
            'datafile_requests_all_updateprior',
            'datafile_requests_mixed_updateprior',
            'datafile_orders',
            'datafile_orders_dontupdateprior',
            'orders_deleted',
            'pdf_orders_deleted',
            'datafile_orders_deleted',
            'pdf_requests_deleted',
            'datafile_requests_deleted',
            'orders',
        ]);
    }

    public function execute(): Collection
    {
        return $this->get();
    }

    public function filename(): string
    {
        return self::QUERY_KEY . '.xlsx';
    }

    public function columnsMapperCallback(string $toExport = null): ?callable
    {
        return function ($model) use ($toExport) {
            $columnsToExport = ! $toExport ? [] : explode(',', $toExport);
            $columnsMap = [
                'company_name' => 'Company Name',
                'A_pdf_requests_singleorder_none_updateprior' => 'Single-order active PDF requests',
                'B_pdf_requests_multiorder_less_all_updateprior' => 'Multi-order active PDF requests',
                'D_datafile_requests_none_updateprior' => 'Active CSV/Tabular requests',
                'E_requests_all_updateprior' => 'Requests all orders revisions',
                'F_requests_rejected' => 'Rejected Requests',
                'G_requests' => 'Total Number of Requests',
                'H_pdf_orders_less_requests_anyupdateprior' => 'Additional PDF Orders',
                'I_datafile_orders_less_requests_anyupdateprior' => 'Additional CSV / Tabular Orders',
                'J_pdf_orders_updateprior' => 'PDF Order Revisions',
                'K_datafile_orders_updateprior' => 'CSV/Tabular Order Revisions',
                'L_pdf_pages_overages' => 'PDF Pages Overages',
                'M_tms_shipments' => 'Number of TMS Shipments',
                'N_requests_deleted' => 'Deleted Requests',
                'O_pdf_orders_including_deleted' => 'Total Number of PDF Orders',
                'P_datafile_orders_including_deleted' => 'Total Number of CSV Orders',
                'Q_pdf_pages_including_deleted' => 'Total Number of PDF Pages Formula',
            ];

            return collect($model->toArray())
                ->filter(fn ($value, $key) => empty($columnsToExport) || in_array($key, $columnsToExport))
                ->mapWithKeys(fn ($value, $key) => [$columnsMap[$key] ?? $key => $value])
                ->toArray();
        };
    }
}
