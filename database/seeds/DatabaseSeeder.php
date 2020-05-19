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
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(OCRRulesTableSeeder::class);
        $this->call(TerminalSeeder::class);
    }
}
