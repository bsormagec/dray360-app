<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use Tests\Seeds\CompaniesSeeder;
use App\Jobs\ImportCompcareAddress;
use Illuminate\Support\Facades\Http;
use App\Jobs\ImportCompcareAddresses;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\Seeds\CompcareTradelinkAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportCompcareAddressesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CompaniesSeeder::class);
        CompaniesSeeder::getTestTradelink()->update(['sync_addresses' => true]);
    }

    /** @test */
    public function it_should_queue_a_job_for_each_address_from_the_endpoint()
    {
        Queue::fake();
        $this->clearTokenCache();
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [['AddressId' => 1]],
                'count' => 2
            ])
            ->push([
                'success' => true,
                'data' => [['AddressId' => 2]],
                'count' => 2
            ])
            ->push([
                'success' => true,
                'data' => [],
                'count' => 2
            ]);

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare()
        ))->handle();

        Queue::assertPushed(ImportCompcareAddress::class, 2);
        Queue::assertPushedOn(
            'imports',
            ImportCompcareAddress::class,
            function (ImportCompcareAddress $job) {
                return in_array($job->addressCode, [1, 2]);
            }
        );
    }

    /** @test */
    public function it_should_only_queue_the_job_for_the_companies_addresses_that_doesnt_exist()
    {
        $this->seed(CompcareTradelinkAddressesSeeder::class);
        Queue::fake();
        $this->clearTokenCache();
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [
                    ['AddressId' => 1],
                    ['AddressId' => 2],
                ],
                'count' => 2
            ])
            ->push(['success' => true, 'data' => [], 'count' => 2]);

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare(),
            true //insert-only
        ))->handle();

        Queue::assertPushed(ImportCompcareAddress::class, 1);
        Queue::assertPushedOn(
            'imports',
            ImportCompcareAddress::class,
            fn (ImportCompcareAddress $job) => $job->addressCode == 2
        );
    }

    /** @test */
    public function it_should_delete_the_addresses_that_dont_come_in_the_api_response()
    {
        $this->seed(CompcareTradelinkAddressesSeeder::class);
        Queue::fake();
        $this->clearTokenCache();
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push([
                'success' => true,
                'data' => [['AddressId' => 2]],
                'count' => 1
            ])
            ->push(['success' => true, 'data' => [], 'count' => 2]);
        $companyAddress = CompanyAddressTMSCode::with('address:id')->first();
        $anotherCompany = factory(CompanyAddressTMSCode::class)->create();

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare(),
            true //insert-only
        ))->handle();

        $this->assertSoftDeleted($companyAddress);
        $this->assertSoftDeleted($companyAddress->address);
        $anotherCompany->fresh(['address']);
        $this->assertNull($anotherCompany->deleted_at);
        $this->assertNull($anotherCompany->address->deleted_at);
    }

    /** @test */
    public function it_should_fail_if_the_list_of_addresses_is_empty()
    {
        $this->seed(CompcareTradelinkAddressesSeeder::class);
        Queue::fake();
        $this->clearTokenCache();
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => ['token' => 'test1']
            ])
            ->push(['success' => true, 'data' => [], 'count' => 2]);
        $companyAddress = CompanyAddressTMSCode::with('address:id')->first();
        factory(CompanyAddressTMSCode::class)->create();

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare(),
            true //insert-only
        ))->handle();

        $this->assertDatabaseHas('t_company_address_tms_code', ['id' => $companyAddress->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('t_addresses', ['id' => $companyAddress->address->id, 'deleted_at' => null]);
    }

    protected function clearTokenCache()
    {
        Cache::forget(RipCms::getTokenCacheKeyFor(CompaniesSeeder::getTestTradelink()));
    }
}
