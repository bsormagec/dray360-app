<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Metrics\ComputeCompanyDailyMetrics;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ComputeCompaniesDailyMetricsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /** @test */
    public function it_should_fail_if_the_dates_are_wrong()
    {
        factory(Company::class)->create();

        $this->artisan('metrics:companies-daily', [
            '--from' => now()->toDateString(),
            '--to' => now()->yesterday()->toDateString(),
        ])->assertExitCode(1);

        Queue::assertNothingPushed();
    }

    /** @test */
    public function it_should_queue_the_job_but_default_to_yesterday_when_no_date_range_is_given()
    {
        $company = factory(Company::class)->create();

        $this->artisan('metrics:companies-daily')->assertExitCode(0);

        Queue::assertPushed(
            ComputeCompanyDailyMetrics::class,
            function (ComputeCompanyDailyMetrics $job) use ($company) {
                return $job->date == now()->yesterday()->toDateString()
                    && $job->companyId == $company->id;
            }
        );
    }

    /** @test */
    public function it_should_queue_the_job_with_and_passing_the_right_attributes()
    {
        $company = factory(Company::class)->create();

        $this->artisan('metrics:companies-daily', [
            '--from' => now()->subDays(2)->toDateString(),
            '--to' => now()->subDays(2)->toDateString(),
        ])->assertExitCode(0);

        Queue::assertPushed(
            ComputeCompanyDailyMetrics::class,
            function (ComputeCompanyDailyMetrics $job) use ($company) {
                return $job->date == now()->subDays(2)->toDateString()
                    && $job->companyId == $company->id;
            }
        );
    }

    /** @test */
    public function it_should_queue_the_jobs_for_each_company_in_each_day_in_the_date_range_given()
    {
        factory(Company::class, 2)->create();

        $this->artisan('metrics:companies-daily', [
            '--from' => now()->subDays(2)->toDateString(),
            '--to' => now()->toDateString(),
        ])->assertExitCode(0);

        Queue::assertPushed(ComputeCompanyDailyMetrics::class, 6);
    }
}
