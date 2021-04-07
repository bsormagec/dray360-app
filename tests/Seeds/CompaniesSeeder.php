<?php

namespace Tests\Seeds;

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;
use Database\Seeders\TmsProvidersSeeder;

class CompaniesSeeder extends Seeder
{
    const
        TEST_CUSHING = 'Test Cushing',
        TEST_TCOMPANIES = 'Test Tcompanies',
        TEST_TRADELINK = 'Test Tradelink',
        TEST_TRADELINK_ONBOARD = 'Test Tradelink Onboard',
        TEST_ITG = 'Test ITG',
        TEST_ITG_ONBOARD = 'Test ITG Onboard';

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
            [
                'tms_provider' => TMSProvider::getCargoWise(),
                'companies' => [
                    self::TEST_ITG,
                    self::TEST_ITG_ONBOARD,
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
        return Company::where('name', self::TEST_CUSHING)->first();
    }

    public static function getTestTcompanies(): Company
    {
        return Company::where('name', self::TEST_TCOMPANIES)->first();
    }

    public static function getTestTradelink(): Company
    {
        return Company::where('name', self::TEST_TRADELINK)->first();
    }

    public static function getTestTradelinkOnboard(): Company
    {
        return Company::where('name', self::TEST_TRADELINK_ONBOARD)->first();
    }

    public static function getTestItg(): Company
    {
        return Company::where('name', self::TEST_ITG)->first();
    }

    public static function getTestItgOnboarding(): Company
    {
        return Company::where('name', self::TEST_ITG_ONBOARD)->first();
    }
}
