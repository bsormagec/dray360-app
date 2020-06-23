<?php

namespace Tests\Feature;

use UsersSeeder;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_authenticate_a_user()
    {
        $this->seed(UsersSeeder::class);

        $user = User::first();

        $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertStatus(200);

        $this->assertEquals(auth()->user()->id, $user->id);
    }

    /** @test */
    public function it_should_logout_a_logged_in_user()
    {
        $this->seed(UsersSeeder::class);

        $user = User::first();
        Sanctum::actingAs($user, ['*']);

        $this->post(route('logout'))
            ->assertStatus(200);

        $this->assertTrue(auth('web')->guest());
    }

    /** @test */
    public function it_should_return_the_logged_in_user_with_its_permissions_and_check_if_its_superadmin()
    {
        $this->seed(UsersSeeder::class);

        $user = User::whereRoleIs('superadmin')->first();
        Sanctum::actingAs($user, ['*']);

        $this->get(route('user'))
            ->assertJsonStructure([
                'id',
                'email',
                'is_superadmin',
                'permissions' => [
                    '*' => [
                        'id',
                        'name',
                        'display_name',
                    ]
                ],
            ]);
    }
}
