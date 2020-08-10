<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\HandleImpersonation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HandleImpersonationTest extends TestCase
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
    public function it_should_handle_impersonation_and_change_the_current_logged_in_user_to_the_impersonated_one()
    {
        $admin = auth()->user();
        app('impersonate')->startWith($this->userToImpersonate);
        Sanctum::actingAs($admin, ['*']);
        $request = Request::create('/test');

        app(HandleImpersonation::class)
            ->handle($request, function ($request) {
            });

        $this->assertEquals($this->userToImpersonate->id, auth()->id());
    }
}
