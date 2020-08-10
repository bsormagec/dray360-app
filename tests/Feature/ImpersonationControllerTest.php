<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImpersonationControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $userToImpersonate;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();

        $this->userToImpersonate = factory(User::class)->create();
        $this->userToImpersonate->attachRole('customer-user');
    }

    /** @test */
    public function it_should_allow_to_start_an_impersonation_session_from_a_request()
    {
        $this->postJson(route('impersonate.start', $this->userToImpersonate->id))
            ->assertStatus(200)
            ->assertJsonFragment([
                'redirect' => '/dashboard'
            ])
            ->assertSessionHas(app('impersonate')->getImpersonatedSessionKey());

        $this->assertEquals($this->userToImpersonate->id, auth()->id());
    }

    /** @test */
    public function it_should_fail_if_the_user_is_not_authorized_to_impersonate()
    {
        $adminId = auth()->id();
        Sanctum::actingAs($this->userToImpersonate);

        $this->postJson(route('impersonate.start', $adminId))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals($this->userToImpersonate->id, auth()->id());
    }

    /** @test */
    public function it_should_allow_to_stop_impersonating_a_user()
    {
        $adminId = auth()->id();
        app('impersonate')->startWith($this->userToImpersonate);

        $this->deleteJson(route('impersonate.stop'))
            ->assertStatus(200)
            ->assertJsonFragment([
                'redirect' => config('nova.path')
            ])
            ->assertSessionMissing(app('impersonate')->getImpersonatedSessionKey());

        $this->assertEquals($adminId, auth()->id());
    }
}
