<?php

use App\Models\TMSProvider;
use Illuminate\Database\Seeder;

class TmsProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            TMSProvider::PROFIT_TOOLS,
            TMSProvider::COMPCARE,
            TMSProvider::CARGOWISE,
        ])->each(function ($tmsProviderName) {
            $tmsProvider = TMSProvider::firstOrCreate(['name' => $tmsProviderName]);
            $this->command->info("TMSProvider {$tmsProviderName}: ".$tmsProvider->id);
        });
    }
}
