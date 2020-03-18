<?php

// to use: ./vendor/bin/phpunit --filter RolesAccessTest

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Artisan;
use database\seeds\RolesAndPermissionsSeeder;

class RolesAccessTest extends TestCase
{

    /**
     * Set up in-memory database instance for testing
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    /**
     * Tear down in-memory unit testing instance
     *
     * @return void
     */
    public function tearDown(): void
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }



    /**
     * Admin can access to admin dashboard
     *
     * @return void
     * @test
     */
    public function adminCanAccessToAdminDashboard()
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
    public function userCanAccessToHome()
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
    public function adminCanAccessYoHome()
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
