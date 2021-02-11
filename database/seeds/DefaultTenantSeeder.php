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
            // itg containers
            'itg_enable_containers' => false,

            'enable_dictionary_items_carrier' => false,
            'enable_dictionary_items_vessel' => false,

            'enable_divisions' => false,

            'hide_field_name_house_bol_hawb' => false,
            'hide_field_name_pickup_by_date' => true,
            'hide_field_name_pickup_by_time' => true,

            // Address Search Booleans
            'address_search_search' => false,
            'address_search_address_filters' => true,
        ];
        $tenant->save();
    }
}
