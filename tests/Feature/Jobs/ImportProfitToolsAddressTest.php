<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Services\Apis\RipCms;
use ProfitToolsCushingSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportProfitToolsAddress;
use Tests\Seeds\ProfitToolsCushingAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportProfitToolsAddressTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProfitToolsCushingSeeder::class);
        $this->seed(ProfitToolsCushingAddressesSeeder::class);
    }

    /** @test */
    public function it_inserts_a_new_address_if_it_doesnt_find_an_existing_one()
    {
        $addresses = $this->getBaseAddresses();
        Queue::fake();
        Http::fakeSequence()
            ->push(['access_token' => 'test'])
            ->push($addresses[0])  //api/ProfitTools/GetCompany/1
            ->push($addresses[1]); //api/ProfitTools/GetCompany/2
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        Cache::forget(RipCms::getTokenCacheKeyFor($company));
        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();
        // Here the cache should be used
        (new ImportProfitToolsAddress(['id' => 2], $company, $tmsProvider))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 2);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $addresses[1]['addr1'],
            'address_line_2' => $addresses[1]['addr2'],
            'city' => $addresses[1]['city'],
            'state' => $addresses[1]['state'],
            'postal_code' => $addresses[1]['zip'],
            'country' => $addresses[1]['country'],
            'location_name' => $addresses[1]['name'],
            'location_phone' => $addresses[1]['phone'],
        ]);
    }

    /** @test */
    public function it_updates_the_already_exiting_address()
    {
        $modifiedAddress = $this->getBaseAddresses()[1];
        $modifiedAddress['id'] = 1;
        Queue::fake();
        Http::fakeSequence()
            ->push(['access_token' => 'test'])
            ->push($this->getBaseAddresses()[0])
            ->push(['access_token' => 'test'])
            ->push($modifiedAddress)
            ->whenEmpty(Http::response(null, 500));
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        Cache::forget(RipCms::getTokenCacheKeyFor($company));
        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();
        Cache::forget(RipCms::getTokenCacheKeyFor($company)); // I want to get the token from ripcms again
        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 1);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $modifiedAddress['addr1'],
            'address_line_2' => $modifiedAddress['addr2'],
            'city' => $modifiedAddress['city'],
            'state' => $modifiedAddress['state'],
            'postal_code' => $modifiedAddress['zip'],
            'country' => $modifiedAddress['country'],
            'location_name' => $modifiedAddress['name'],
            'location_phone' => $modifiedAddress['phone'],
        ]);
    }

    protected function getBaseAddresses()
    {
        return  [
            [
                "id" => 1,
                "name" => "UPG3   Z 6",
                "addr1" => "2701 INTERMODAL DRIVE",
                "addr2" => null,
                "city" => "ROCHELLE",
                "state" => "IL",
                "zip" => "61068",
                "country" => null,
                "phone" => "8155612421    "
            ], [
                "id" => 2,
                "name" => "USP   AS 23",
                "addr1" => "2704 ANOTHER DRIVE",
                "addr2" => null,
                "city" => "CHICAGO",
                "state" => "IL",
                "zip" => "61098",
                "country" => null,
                "phone" => "23423    "
            ]
        ];
    }
}
