<?php

use App\Models\Address;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;

class ProfitToolsCushingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provider = TMSProvider::firstOrCreate(['name' => TMSProvider::PROFIT_TOOLS]);
        $this->command->info("TMSProvider Id: {$provider->id}");

        collect([
            Company::CUSHING,
            Company::TCOMPANIES_DEV,
            Company::POLARIS,
            Company::IXT_ONBOARDING,
        ])->each(function ($companyName) {
            $company = Company::firstOrCreate(
                ['name' => $companyName],
                ['t_address_id' => Address::create()->id]
            );

            $this->command->info("{$companyName} Id: {$company->id}");
        });
    }
}
