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
        $cushing = Company::firstOrCreate(
            ['name' => Company::CUSHING],
            ['t_address_id' => Address::create()->id]
        );
        $tcompaniesDev = Company::firstOrCreate(
            ['name' => Company::TCOMPANIES_DEV],
            ['t_address_id' => Address::create()->id]
        );

        $this->command->info("TMSProvider Id: {$provider->id}");
        $this->command->info("Cushing Id: {$cushing->id}");
        $this->command->info("TCompaniesDev Id: {$tcompaniesDev->id}");
    }
}
