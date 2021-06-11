<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Queries\Metrics\CompaniesDailyMetricsReportQuery;

class MetricsReportsController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! auth()->user()->isAbleTo('metrics-view')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $metricsQueries = [
            CompaniesDailyMetricsReportQuery::QUERY_KEY => CompaniesDailyMetricsReportQuery::class,
        ];

        $data = $request->validate([
            'metric' => ['required', Rule::in(array_keys($metricsQueries))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return JsonResource::collection(
            (new $metricsQueries[$data['metric']]($data))->execute()
        );
    }
}
