<?php

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DefaultTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenant = Tenant::firstOrCreate(['id' => Tenant::DEFAULT_ID], ['name' => 'default']);
        $tenant->configuration ??= [];
        $tenant->configuration = $tenant->configuration + [
            'contact_address' => null,
            'contact_url' => null,
            'contact_zip' => null,
            'contact_city' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'contact_state' => null,
            'logo1' => null,
            'logo2' => null,
            'display_name' => null,
            'favicon' => null,
            'title' => null,
            'primary_color' => null,
            'secondary_color' => null,
            'accent_color' => null,

            // Profittools templates
            'profit_tools_enable_templates' => false,
            // Profittools templates
            'itg_enable_containers' => false,

            // Address Search Booleans
            'address_search_location_name' => false,
            'address_search_city' => false,
            'address_search_postal_code' => false,
            'address_search_address' => false,
            'address_search_state' => false,
            'show_orders_tab_first' => false,
        ];
        $tenant->save();
    }
}
