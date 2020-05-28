<?php

namespace Tests\Seeds;

use App\User;
use RolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $user = factory(User::class)->create();
        $user->assignRole('admin');
    }
}
