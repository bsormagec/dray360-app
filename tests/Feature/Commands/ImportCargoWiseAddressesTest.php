<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Tests\Seeds\CompaniesSeeder;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportItgCargoWiseAddress;
use Illuminate\Support\Facades\Storage;
use Tests\Seeds\CargoWiseItgAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportCargoWiseAddressesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CompaniesSeeder::class);
        $company = tap(CompaniesSeeder::getTestItg())->update(['sync_addresses' => true]);
        CompaniesSeeder::getTestItgOnboarding()->update(['sync_addresses' => false]);

        Storage::fake('s3-file-ingestion');
        Storage::disk('s3-file-ingestion')
            ->put(
                "/company_{$company->id}/addressfiles/itg-addresses-test.xlsx",
                fopen(base_path('tests/files/itg-addresses-test.xlsx'), 'r')
            );
    }

    /** @test */
    public function it_should_queue_a_job_for_each_address_from_the_xlsx_file()
    {
        Queue::fake();

        $this->artisan('import:cargowise-addresses')->assertExitCode(0);

        Queue::assertPushed(ImportItgCargoWiseAddress::class, 2);
        Queue::assertPushedOn(
            'imports',
            ImportItgCargoWiseAddress::class,
            function (ImportItgCargoWiseAddress $job) {
                return in_array($job->addressCode, ['ANNPUBARB', 'SEASHITOA']);
            }
        );
    }

    /** @test */
    public function it_should_only_queue_the_job_for_the_companies_addresses_that_doesnt_exist()
    {
        $this->seed(CargoWiseItgAddressesSeeder::class);
        Queue::fake();

        $this->artisan('import:cargowise-addresses', [
            '--insert-only' => true,
            '--company-name' => CompaniesSeeder::getTestItg()->name,
        ])->assertExitCode(0);

        Queue::assertPushed(ImportItgCargoWiseAddress::class, 1);
        Queue::assertPushedOn(
            'imports',
            ImportItgCargoWiseAddress::class,
            fn (ImportItgCargoWiseAddress $job) => $job->addressCode == 'SEASHITOA'
        );
    }

    /** @test */
    public function it_should_delete_the_addresses_that_doesnt_come_in_the_file()
    {
        $this->seed(CargoWiseItgAddressesSeeder::class);
        Queue::fake();
        $itg = CompaniesSeeder::getTestItg();
        Storage::disk('s3-file-ingestion')
            ->put(
                "/company_{$itg->id}/addressfiles/itg-addresses-test-deleted.xlsx",
                fopen(base_path('tests/files/itg-addresses-test-deleted.xlsx'), 'r')
            );

        $companyAddress = CompanyAddressTMSCode::with('address:id')->first();
        $anotherCompany = factory(CompanyAddressTMSCode::class)->create();

        $this->artisan('import:cargowise-addresses', [
            '--company-name' => $itg->name,
        ])->assertExitCode(0);

        $this->assertSoftDeleted($companyAddress);
        $this->assertSoftDeleted($companyAddress->address);
        $anotherCompany->fresh(['address']);
        $this->assertNull($anotherCompany->deleted_at);
        $this->assertNull($anotherCompany->address->deleted_at);
    }
}
