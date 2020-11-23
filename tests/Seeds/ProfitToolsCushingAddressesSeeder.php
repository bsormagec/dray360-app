<?php

namespace Tests\Seeds;

use App\Models\Address;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;
use App\Models\CompanyAddressTMSCode;

class ProfitToolsCushingAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = CompaniesSeeder::getTestCushing();
        $tmsProvider = TMSProvider::getProfitTools();

        $baseAddress = [
            "id" => 1,
            "name" => "UPG3   Z 6",
            "addr1" => "2701 INTERMODAL DRIVE",
            "addr2" => null,
            "city" => "ROCHELLE",
            "state" => "IL",
            "zip" => "61068",
            "country" => null,
            "phone" => "8155612421    "
        ];

        $address = Address::create([
            'address_line_1' => $baseAddress['addr1'],
            'address_line_2' => $baseAddress['addr2'],
            'city' => $baseAddress['city'],
            'state' => $baseAddress['state'],
            'postal_code' => $baseAddress['zip'],
            'country' => $baseAddress['country'],
            'location_name' => $baseAddress['name'],
            'location_phone' => $baseAddress['phone'],
        ]);

        factory(CompanyAddressTMSCode::class)->create([
            't_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            'company_address_tms_code' => $baseAddress['id'],
        ]);
    }
}
