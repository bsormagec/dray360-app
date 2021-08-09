<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Tests\Seeds\UsersSeeder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersSeeder::class);
        $this->user = User::whereRoleIs('customer-user')->first();
        $this->user->setCompany(factory(Company::class)->create(), true);
        Config::set('sanctum.stateful', [$this->user->company->domain->hostname, 'anotherdomain.com']);
    }

    /** @test */
    public function it_should_authenticate_a_user()
    {
        $this->postJson(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => 'password',
            ],
            [
                'Referer' => $this->user->company->domain->hostname,
            ]
        )
        ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertEquals(auth()->user()->id, $this->user->id);
    }

    /** @test */
    public function it_should_fail_and_return_a_json_with_a_redirect()
    {
        $this->post(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => 'password',
            ],
            [
                'Referer' => 'anotherdomain.com',
            ]
        )
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonFragment([
            'redirect' => $this->user->company->domain->hostname,
        ]);
    }

    /** @test */
    public function it_should_fail_if_user_is_inactive()
    {
        $this->user->deactivate();
        $this->post(
            route('login'),
            [
                'email' => $this->user->email,
                'password' => 'password',
            ],
            [
                'Referer' => $this->user->company->domain->hostname,
            ]
        )
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_should_logout_a_logged_in_user()
    {
        $user = User::first();
        Sanctum::actingAs($user, ['*']);

        $this->post(route('logout'))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertTrue(auth('web')->guest());
    }

    /** @test */
    public function it_should_return_the_logged_in_user_with_its_permissions_and_check_if_its_superadmin()
    {
        $user = User::whereRoleIs('superadmin')->first();
        Sanctum::actingAs($user, ['*']);

        $this->get(route('user'))
            ->assertJsonStructure([
                'id',
                'email',
                'is_superadmin',
                'configuration',
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
