<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Tests\Seeds\CompaniesSeeder;
use App\Models\CompanyDailyMetric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Queries\Metrics\CompaniesDailyMetricsReportQuery;

class MetricsReportsControllerCompanyDailyMetricsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
    }

    /** @test */
    public function it_should_return_the_daily_metrics_for_the_company()
    {
        $company = Company::first();
        $data = factory(CompanyDailyMetric::class)->createMany([
            ['metric_date' => now()->subDays(1), 't_company_id' => $company->id],
            ['metric_date' => now()->subDays(2), 't_company_id' => $company->id],
            ['metric_date' => now()->subDays(3), 't_company_id' => $company->id],
        ]);

        $this->getJson(route('metrics.index', [
            'metric' => CompaniesDailyMetricsReportQuery::QUERY_KEY,
            'filter[company_id]' => $company->id,
            'start_date' => now()->subDays(1)->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(),
        ]))->assertJsonFragment([
            'requests_deleted' => $data->first()->requests_deleted . "",
            'datafile_requests_mixed_updateprior' => $data->first()->datafile_requests_mixed_updateprior . "",
        ]);
        $this->getJson(route('metrics.index', [
            'metric' => CompaniesDailyMetricsReportQuery::QUERY_KEY,
            'filter[company_id]' => $company->id,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(),
        ]))->assertJsonFragment([
            'requests_deleted' => $data->sum('requests_deleted') . "",
            'datafile_requests_mixed_updateprior' => $data->sum('datafile_requests_mixed_updateprior') . "",
        ]);
    }
}
