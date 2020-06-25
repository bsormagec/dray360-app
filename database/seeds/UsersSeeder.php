<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        $this->call(LaratrustSeeder::class);

        $user = factory(\App\Models\User::class)->create();
        $user->attachRole('customer-user');

        $admin = factory(\App\Models\User::class)->create();
        $admin->attachRole('superadmin');
    }
}
