<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\Address;
use App\Models\TMSProvider;
use Tests\Seeds\CompaniesSeeder;
use App\Models\CompanyAddressTMSCode;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Imports\ImportCompcareAddress;
use Tests\Seeds\CompcareTradelinkAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportCompcareAddressTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CompaniesSeeder::class);
        $this->seed(CompcareTradelinkAddressesSeeder::class);
        Queue::fake();
    }

    /** @test */
    public function it_inserts_a_new_address_if_it_doesnt_find_an_existing_one()
    {
        $addresses = CompcareTradelinkAddressesSeeder::getBaseAddresses();
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();

        (new ImportCompcareAddress($addresses[0], $tmsProvider->id, $company))->handle();
        (new ImportCompcareAddress($addresses[1], $tmsProvider->id, $company))->handle();

        $this->assertEquals(2, CompanyAddressTMSCode::count());
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $addresses[1]['AddressLine1'],
            'address_line_2' => $addresses[1]['AddressLine2'],
            'city' => $addresses[1]['City']['CityName'],
            'state' => $addresses[1]['State']['StateCode'],
            'postal_code' => $addresses[1]['PostalCode'],
            'country' => $addresses[1]['Country']['CountryCode'],
            'location_name' => $addresses[1]['Entity']['EntityName'],
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => 0,
            'is_cc_payor' => 0,
            'is_cc_customer' => 0,
            'is_cc_ssrr' => 0,
            'is_cc_carrier' => 0,
            'is_cc_consignee' => 1,
            'is_cc_driver' => 0,
            'is_cc_shipper' => 1,
            'is_cc_vendor' => 0,
        ]);
    }

    /** @test */
    public function it_creates_a_new_entry_for_updated_addresses()
    {
        $addresses = CompcareTradelinkAddressesSeeder::getBaseAddresses();
        $modifiedAddress = $addresses[1];
        $modifiedAddress['EntityId'] = 1;
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();
        $addressCount = Address::count();

        (new ImportCompcareAddress($addresses[0], $tmsProvider->id, $company))->handle();
        (new ImportCompcareAddress($modifiedAddress, $tmsProvider->id, $company))->handle();

        $this->assertEquals(1, CompanyAddressTMSCode::count());
        $this->assertEquals($addressCount + 1, Address::count());
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $modifiedAddress['AddressLine1'],
            'address_line_2' => $modifiedAddress['AddressLine2'],
            'city' => $modifiedAddress['City']['CityName'],
            'state' => $modifiedAddress['State']['StateCode'],
            'postal_code' => $modifiedAddress['PostalCode'],
            'country' => $modifiedAddress['Country']['CountryCode'],
            'location_name' => $modifiedAddress['Entity']['EntityName'],
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => 0,
            'is_cc_payor' => 0,
            'is_cc_customer' => 0,
            'is_cc_ssrr' => 0,
            'is_cc_carrier' => 0,
            'is_cc_consignee' => 1,
            'is_cc_driver' => 0,
            'is_cc_shipper' => 1,
            'is_cc_vendor' => 0,
        ]);
    }
}
