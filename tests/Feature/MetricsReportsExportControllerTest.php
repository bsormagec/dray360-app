<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Http\Response;
use Tests\Seeds\CompaniesSeeder;
use App\Models\CompanyDailyMetric;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Queries\Metrics\CompaniesDailyMetricsReportQuery;

class MetricsReportsExportControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        $this->seed(CompaniesSeeder::class);
    }

    /** @test */
    public function it_should_return_a_file_download_with_the_excel_file_of_the_report()
    {
        Storage::fake('local');
        $company = Company::first();
        $data = factory(CompanyDailyMetric::class)->createMany([
            ['metric_date' => now()->subDays(1), 't_company_id' => $company->id],
            ['metric_date' => now()->subDays(2), 't_company_id' => $company->id],
            ['metric_date' => now()->subDays(3), 't_company_id' => $company->id],
        ]);
        // MetricsReportsExportController

        $this->get(route('metrics-export.index', [
            'metric' => CompaniesDailyMetricsReportQuery::QUERY_KEY,
            'filter[company_id]' => $company->id,
            'start_date' => now()->subDays(1)->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(),
        ]))->assertStatus(Response::HTTP_OK);
    }
}
