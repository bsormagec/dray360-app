<?php

namespace Tests\Feature\Jobs\Metrics;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Support\Carbon;
use App\Jobs\Metrics\ComputeCompanyDailyMetrics;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ComputeCompanyDailyMetricsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_save_the_daily_metrics_for_a_given_company_on_a_given_date()
    {
        $company = factory(Company::class)->create();
        $date = Carbon::createFromDate('2021-04-01');

        (new ComputeCompanyDailyMetrics($date, $company))->handle();

        $this->assertDatabaseCount('t_company_daily_metrics', 1);
        $this->assertDatabaseHas('t_company_daily_metrics', [
            't_company_id' => $company->id,
            'metric_date' => $date->toDateString(),
        ]);
    }
}
