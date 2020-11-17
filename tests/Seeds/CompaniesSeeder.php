<?php

namespace Tests\Seeds;

use App\Models\Address;
use App\Models\Company;
use TmsProvidersSeeder;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    const
        TEST_CUSHING = 'Test Cushing',
        TEST_TCOMPANIES = 'Test Tcompanies',
        TEST_TRADELINK = 'Test Tradelink',
        TEST_TRADELINK_ONBOARD = 'Test Tradelink Onboard';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TmsProvidersSeeder::class);
        $tmsProviderCompanies = [
            [
                'tms_provider' => TMSProvider::getProfitTools(),
                'companies' => [
                    'Test Cushing',
                    'Test Tcompanies',
                ],
            ],
            [
                'tms_provider' => TMSProvider::getCompcare(),
                'companies' => [
                    'Test Tradelink',
                    'Test Tradelink Onboard',
                ],
            ],
        ];

        collect($tmsProviderCompanies)->each(function ($item) {
            $this->createCompanies($item['tms_provider'], $item['companies']);
        });
    }

    protected function createCompanies(TMSProvider $tmsProvider, array $companies)
    {
        collect($companies)->each(function ($companyName) use ($tmsProvider) {
            Company::firstOrCreate(
                ['name' => $companyName],
                [
                        't_address_id' => Address::create()->id,
                        'default_tms_provider_id' => $tmsProvider->id,
                    ]
            );
        });
    }

    public static function getTestCushing(): Company
    {
        return Company::where('name', 'Test Cushing')->first();
    }

    public static function getTestTcompanies(): Company
    {
        return Company::where('name', 'Test Tcompanies')->first();
    }

    public static function getTestTradelink(): Company
    {
        return Company::where('name', 'Test Tradelink')->first();
    }

    public static function getTestTradelinkOnboard(): Company
    {
        return Company::where('name', 'Test Tradelink Onboard')->first();
    }
}
