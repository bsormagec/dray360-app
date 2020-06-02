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
        $provider = TMSProvider::firstOrCreate(['name' => 'Profit Tools']);
        $company = Company::firstOrCreate(
            ['name' => 'Cushing'],
            ['t_address_id' => Address::create()->id]
        );

        $this->command->info("TMSProvider Id: {$provider->id}");
        $this->command->info("Company Id: {$company->id}");
    }
}
