<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersStatusControllerTest extends TestCase
{
    use DatabaseTransactions;
    protected User $customerAdmin;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginCustomerAdmin();
        $this->customerAdmin = auth()->user();
    }

    /** @test */
    public function it_should_allow_to_deactivate_a_user()
    {
        $user = factory(User::class)->create();
        $user->setCompany($this->customerAdmin->company)->save();

        $this->putJson(route('users.status.update', $user->id), ['active' => false])
            ->assertStatus(Response::HTTP_OK);

        $user->refresh();
        $this->assertNotNull($user->deactivated_at);
    }

    /** @test */
    public function it_should_fail_if_not_authorized()
    {
        $user = factory(User::class)->create();

        $this->putJson(route('users.status.update', $user->id), ['active' => false])
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $user->refresh();
        $this->assertNull($user->deactivated_at);
    }
}
