<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Imports\ImportCompcareAddresses;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportCompcareAddressesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CompaniesSeeder::class);
        CompaniesSeeder::getTestTradelink()->update(['sync_addresses' => true]);
        CompaniesSeeder::getTestTradelinkOnboard()->update(['sync_addresses' => true]);
    }

    /** @test */
    public function it_should_queue_one_job_per_company_associated_with_compcare()
    {
        Queue::fake();

        $this->artisan('import:compcare-addresses')->assertExitCode(0);

        Queue::assertPushed(ImportCompcareAddresses::class, 2);
        Queue::assertPushedOn(
            'imports',
            ImportCompcareAddresses::class,
            function (ImportCompcareAddresses $job) {
                return in_array($job->companyId, [
                    CompaniesSeeder::getTestTradelink()->id,
                    CompaniesSeeder::getTestTradelinkOnboard()->id,
                ]);
            }
        );
    }

    /** @test */
    public function it_should_queue_insert_only_jobs()
    {
        Queue::fake();

        $this->artisan('import:compcare-addresses', [
            '--insert-only' => true,
            '--company-name' => CompaniesSeeder::TEST_TRADELINK,
        ])->assertExitCode(0);

        Queue::assertPushed(ImportCompcareAddresses::class, 1);
        Queue::assertPushedOn(
            'imports',
            ImportCompcareAddresses::class,
            function (ImportCompcareAddresses $job) {
                return $job->companyId == CompaniesSeeder::getTestTradelink()->id
                    && $job->insertOnly === true;
            }
        );
    }
}
