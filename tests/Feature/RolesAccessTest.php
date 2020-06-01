<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolesAccessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set up in-memory database instance for testing
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    // http://local.ordermaster.com/api/user
    // http://local.ordermaster.com/login

    /**
     * Admin can access to admin dashboard
     *
     * @return void
     * @test
     */
    public function adminCanAccessAdminDashboard()
    {
        //Having
        $adminUser = factory(User::class)->create();

        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        //When
        $response = $this->get(route('admin.dashboard'));

        //Then
        $response->assertOk();
    }

    /**
     * User can access home route
     *
     * @return void
     * @test
     */
    public function userCanAccessHome()
    {
        //Having
        $user = factory(User::class)->create();

        $user->assignRole('user');

        $this->actingAs($user);

        //When
        $response = $this->get(route('home'));

        //Then
        $response->assertOk();
    }

    /**
     * Admin can access home route
     *
     * @return void
     * @test
     */
    public function adminCanAccessHome()
    {
        //Having
        $adminUser = factory(User::class)->create();

        $adminUser->assignRole('admin');

        $this->actingAs($adminUser);

        //When
        $response = $this->get(route('home'));

        //Then
        $response->assertOk();
    }

    /** @nonfunctional_test */
    public function user_must_login_to_access_to_admin_dashboard()
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect('login');
    }

    /** @nonfunctional_test */
    public function users_cannot_access_to_admin_dashboard()
    {
        //Having
        $user = factory(User::class)->create();

        $user->assignRole('user');

        $this->actingAs($user);

        //When
        $response = $this->get(route('admin.dashboard'));

        //Then
        $response->assertForbidden();
    }
}
