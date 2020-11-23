<?php

namespace Tests\Seeds;

use App\Models\Address;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;
use App\Models\CompanyAddressTMSCode;

class CompcareTradelinkAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();

        $baseAddress = [
            'AddressId' => 1,
            'ExternalSystemAddressId' => '10308835',
            'AddressLine1' => '4912 RAILROAD STREET',
            'AddressLine2' => '',
            'CityName' => 'LAPORTE',
            'PostalCode' => '77571',
            'City' => [
                'CityName' => 'LAPORTE',
            ],
            'Country' => [
                'CountryCode' => 'USA',
            ],
            'State' => [
                'StateCode' => 'TX',
            ]
        ];

        $address = Address::create([
            'address_line_1' => $baseAddress['AddressLine1'],
            'address_line_2' => $baseAddress['AddressLine2'],
            'city' => $baseAddress['City']['CityName'],
            'state' => $baseAddress['State']['StateCode'],
            'postal_code' => $baseAddress['PostalCode'],
            'country' => $baseAddress['Country']['CountryCode'],
            'location_name' => null,
            'location_phone' => null,
        ]);

        factory(CompanyAddressTMSCode::class)->create([
            't_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            'company_address_tms_code' => $baseAddress['AddressId'],
        ]);
    }
}
