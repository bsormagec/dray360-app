<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Services\Apis\RipCms;
use ProfitToolsCushingSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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
        Cache::forget(RipCms::TOKEN_CACHE_KEY);
        Http::fake([
            'https://www.ripcms.com/token*' => Http::response(['access_token' => 'test']),
            'https://www.ripcms.com/api/*' => Http::response([
                [ "id" => 1, "name" => "UPG3   Z 6"],
                ["id" => 2, "name" => "WSI WAREHOUSE"],
            ]),
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
