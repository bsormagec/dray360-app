<?php

namespace Tests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Login an admin user to sanctum.
     *
     * @return void
     */
    protected function loginAdmin()
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        Sanctum::actingAs($user, ['*']);
    }
}
