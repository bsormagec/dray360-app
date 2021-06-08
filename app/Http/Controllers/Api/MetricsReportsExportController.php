<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Queries\Metrics\CompaniesDailyMetricsReportQuery;

class MetricsReportsExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $metricsQueries = [
            CompaniesDailyMetricsReportQuery::QUERY_KEY => CompaniesDailyMetricsReportQuery::class,
        ];

        $data = $request->validate([
            'metric' => ['required', Rule::in(array_keys($metricsQueries))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'to_export' => ['sometimes', 'nullable', 'string'],
        ]);

        $metricQuery = new $metricsQueries[$data['metric']]($data);

        return (new FastExcel($metricQuery->execute()))
            ->download($metricQuery->filename(), $metricQuery->columnsMapperCallback($data['to_export'] ?? null));
    }
}
