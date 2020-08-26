<?php

namespace Tests;

use UsersSeeder;
use App\Models\User;
use App\Models\Company;
use DefaultTenantSeeder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DefaultTenantSeeder::class);
    }

    /**
     * Login an admin user to sanctum.
     *
     * @return void
     */
    protected function loginAdmin()
    {
        $this->seedTestUsers();

        $user = User::whereRoleIs('superadmin')->first();
        Sanctum::actingAs($user, ['*']);
    }

    protected function seedTestUsers()
    {
        if (User::count() > 0) {
            return;
        }

        config()->set('laratrust_seeder.truncate_tables', false);
        $this->seed(UsersSeeder::class);
    }

    protected function loginNoAdmin()
    {
        $this->seedTestUsers();

        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);
    }

    protected function loginCustomerAdmin()
    {
        $this->seedTestUsers();

        $user = User::whereRoleIs('customer-admin')->first();
        $user->setCompany(factory(Company::class)->create())->save();
        Sanctum::actingAs($user);
    }
}
