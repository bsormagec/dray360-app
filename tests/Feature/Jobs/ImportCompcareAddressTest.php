<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\TMSProvider;
use Tests\Seeds\CompaniesSeeder;
use App\Jobs\ImportCompcareAddress;
use Illuminate\Support\Facades\Queue;
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
        $addresses = $this->getBaseAddresses();
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();

        (new ImportCompcareAddress($addresses[0], $tmsProvider->id, $company))->handle();
        (new ImportCompcareAddress($addresses[1], $tmsProvider->id, $company))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 2);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $addresses[1]['AddressLine1'],
            'address_line_2' => $addresses[1]['AddressLine2'],
            'city' => $addresses[1]['City']['CityName'],
            'state' => $addresses[1]['State']['StateCode'],
            'postal_code' => $addresses[1]['PostalCode'],
            'country' => $addresses[1]['Country']['CountryCode'],
            'location_name' => null,
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => 0,
        ]);
    }

    /** @test */
    public function it_updates_the_already_exiting_address()
    {
        $modifiedAddress = $this->getBaseAddresses()[1];
        $modifiedAddress['AddressId'] = 1;
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();

        (new ImportCompcareAddress($this->getBaseAddresses()[0], $tmsProvider->id, $company))->handle();
        (new ImportCompcareAddress($modifiedAddress, $tmsProvider->id, $company))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 1);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $modifiedAddress['AddressLine1'],
            'address_line_2' => $modifiedAddress['AddressLine2'],
            'city' => $modifiedAddress['City']['CityName'],
            'state' => $modifiedAddress['State']['StateCode'],
            'postal_code' => $modifiedAddress['PostalCode'],
            'country' => $modifiedAddress['Country']['CountryCode'],
            'location_name' => null,
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => 0,
        ]);
    }

    protected function getBaseAddresses()
    {
        return  [
            [
                'AddressId' => 1,
                'ExternalSystemAddressId' => '10308835',
                'AddressLine1' => '4912 RAILROAD STREET',
                'AddressLine2' => '',
                'CityName' => 'LAPORTE',
                'City' => null,
                'PostalCode' => '77571',
                'Country' => [
                    'CountryCode' => 'USA',
                ],
                'State' => [
                    'StateCode' => 'TX',
                ]
            ], [
                'AddressId' => 2,
                'ExternalSystemAddressId' => '10308836',
                'AddressLine1' => '1234 EASY ST',
                'AddressLine2' => '1234 Easy test',
                'CityName' => 'Abbeville',
                'PostalCode' => '77571',
                'City' => [
                    'CityName' => 'Abbeville',
                ],
                'Country' => [
                    'CountryCode' => 'USA',
                ],
                'State' => [
                    'StateCode' => 'CA',
                ]
            ]
        ];
    }
}
