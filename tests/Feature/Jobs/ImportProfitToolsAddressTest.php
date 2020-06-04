<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Company;
use App\Models\TMSProvider;
use ProfitToolsCushingSeeder;
use Illuminate\Support\Facades\Http;
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
        Http::fake([
            'https://www.ripcms.com/api/ProfitTools/GetCompany/1' => Http::response($addresses[0]),
            'https://www.ripcms.com/api/ProfitTools/GetCompany/2' => Http::response($addresses[1])
        ]);
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();
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
        $address = $this->getBaseAddresses()[1];
        $address['id'] = 1;
        Queue::fake();
        Http::fake(['*' => Http::response($address)]);
        $company = Company::getCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();
        (new ImportProfitToolsAddress(['id' => 1], $company, $tmsProvider))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 1);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $address['addr1'],
            'address_line_2' => $address['addr2'],
            'city' => $address['city'],
            'state' => $address['state'],
            'postal_code' => $address['zip'],
            'country' => $address['country'],
            'location_name' => $address['name'],
            'location_phone' => $address['phone'],
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
