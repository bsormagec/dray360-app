<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TmsProvidersSeeder::class);
        $this->call(AddressLocationnameOverrideSeeder::class);
        $this->call(DivisionCodeSeeder::class);
        $this->call(LaratrustSeeder::class);
        $this->call(DefaultTenantSeeder::class);
    }
}
