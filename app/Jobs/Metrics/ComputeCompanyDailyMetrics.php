<?php

namespace App\Jobs\Metrics;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Models\CompanyDailyMetric;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Queries\Metrics\RequestsOrdersDeletedMetricQuery;
use App\Queries\Metrics\RequestsOrdersSharedMetricsQuery;

class ComputeCompanyDailyMetrics implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public $tries = 3;
    public $timeout = 120;

    public string $date;
    public int $companyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Carbon $date, Company $company)
    {
        $this->date = $date->toDateString();
        $this->companyId = $company->id;
        $this->onQueue('metrics');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $metricsQueries = [
            RequestsOrdersSharedMetricsQuery::class,
            RequestsOrdersDeletedMetricQuery::class,
        ];
        $metrics = [];
        $date = Carbon::createFromDate($this->date);

        foreach ($metricsQueries as $metricQuery) {
            $metrics += (new $metricQuery())($date, $this->companyId);
        }

        CompanyDailyMetric::updateOrCreate([
            't_company_id' => $this->companyId,
            'metric_date' => $this->date,
        ], $metrics);
    }
}
