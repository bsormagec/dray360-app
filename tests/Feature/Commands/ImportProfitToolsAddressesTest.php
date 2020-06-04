<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use ProfitToolsCushingSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportProfitToolsAddress;
use Tests\Seeds\ProfitToolsCushingAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportProfitToolsAddressesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProfitToolsCushingSeeder::class);
        $this->seed(ProfitToolsCushingAddressesSeeder::class);
    }

    /** @test */
    public function it_should_queue_a_job_for_each_address_from_the_endpoint()
    {
        Queue::fake();
        Http::fake([
            'https://www.ripcms.com/*' => Http::response([
                [ "id" => 1, "name" => "UPG3   Z 6"],
                ["id" => 2, "name" => "WSI WAREHOUSE"],
            ])
        ]);

        $this->artisan('import:profit-tools-addresses')->assertExitCode(0);

        Queue::assertPushedOn(
            'imports',
            ImportProfitToolsAddress::class,
            function (ImportProfitToolsAddress $job) {
                return in_array($job->addressCode, [1, 2]);
            }
        );
    }
}
