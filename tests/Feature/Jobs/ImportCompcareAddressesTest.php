<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Http;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Imports\ImportCompcareAddress;
use App\Jobs\Imports\ImportCompcareAddresses;
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
    public function it_should_requeue_itself_with_the_next_page()
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
                'data' => [['EntityId' => 1]],
                'count' => 2
            ]);

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare()
        ))->handle();

        Queue::assertPushed(
            ImportCompcareAddresses::class,
            function (ImportCompcareAddresses $job) {
                return $job->page == 2
                    && $job->apiAddresses[0] == 1
                    && ! $job->insertOnly
                    && $job->tmsProviderId == TMSProvider::getCompcare()->id
                    && $job->companyId == CompaniesSeeder::getTestTradelink()->id;
            }
        );
    }

    /** @test */
    public function it_should_only_delete_addresses_if_response_is_empty_and_the_api_addresses_has_values()
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
        $anotherCompany = factory(CompanyAddressTMSCode::class)->create();

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare(),
            true, //insert-only,
            2, // page 2
            [2] // EntityId 2
        ))->handle();

        $this->assertSoftDeleted($companyAddress);
        $anotherCompany->fresh(['address']);
        $this->assertNull($anotherCompany->deleted_at);
        Queue::assertNothingPushed();
    }

    /** @test */
    public function it_should_not_do_anything_if_the_addresses_is_empty_and_response_is_empty()
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
        Queue::assertNothingPushed();
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
                'data' => [
                    ['EntityId' => 1],
                    ['EntityId' => 2]
                ],
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
    public function it_should_filter_the_secondary_addresses_from_the_addresses_to_import()
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
                'data' => [
                    [
                        'EntityId' => 1,
                        'LocationType' => ['LocationTypeCode' => "S"],
                    ], [
                        'EntityId' => 1,
                        'LocationType' => ['LocationTypeCode' => "P"],
                    ]
                ],
                'count' => 2
            ]);

        (new ImportCompcareAddresses(
            CompaniesSeeder::getTestTradelink(),
            TMSProvider::getCompcare()
        ))->handle();

        Queue::assertPushed(ImportCompcareAddress::class, 1);
        Queue::assertPushedOn(
            'imports',
            ImportCompcareAddress::class,
            function (ImportCompcareAddress $job) {
                return $job->addressCode == 1
                    && $job->address['LocationType']['LocationTypeCode'] == 'P';
            }
        );
    }

    /** @test */
    public function it_should_only_queue_the_job_for_the_companies_addresses_that_dont_exist()
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
                    ['EntityId' => 1],
                    ['EntityId' => 2],
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

    protected function clearTokenCache()
    {
        Cache::forget(RipCms::getTokenCacheKeyFor(CompaniesSeeder::getTestTradelink()));
    }
}
