<?php

namespace Tests;

use UsersSeeder;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
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
        config()->set('laratrust_seeder.truncate_tables', false);
        $this->seed(UsersSeeder::class);

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'superadmin');
        })->first();
        Sanctum::actingAs($user, ['*']);
    }
}
