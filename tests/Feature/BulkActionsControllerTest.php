<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class BulkActionsControllerTest extends TestCase
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
    public function it_should_allow_to_execute_a_predefined_bulk_action()
    {
        $users = factory(User::class, 5)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);

        $this->postJson(route('bulk-actions'), [
                'action' => 'users.deactivate',
                'models' => $users->pluck('id')->toArray()
            ])
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $users->each(function ($user) {
            $user->refresh();
            $this->assertNotNull($user->deactivated_at);
        });
    }

    /** @test */
    public function it_should_delete_users_in_bulk()
    {
        $users = factory(User::class, 5)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);

        $this->postJson(route('bulk-actions'), [
            'action' => 'users.delete',
            'models' => $users->pluck('id')->toArray()
        ])
        ->assertStatus(Response::HTTP_NO_CONTENT);

        $users->each(function ($user) {
            $this->assertSoftDeleted('users', ['id' => $user->id]);
        });
    }

    /** @test */
    public function it_should_activate_users_in_bulk()
    {
        $users = factory(User::class, 5)->create([
            Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId(),
            'deactivated_at' => now(),
        ]);

        $this->postJson(route('bulk-actions'), [
            'action' => 'users.activate',
            'models' => $users->pluck('id')->toArray()
        ])
        ->assertStatus(Response::HTTP_NO_CONTENT);

        $users->each(function ($user) {
            $user->refresh();
            $this->assertNull($user->deactivated_at);
        });
    }

    /** @test */
    public function it_should_send_the_reset_password_emails_in_bulk()
    {
        Notification::fake();
        $users = factory(User::class, 5)->create([
               Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId(),
               'deactivated_at' => now(),
           ]);

        $this->postJson(route('bulk-actions'), [
               'action' => 'users.reset_password',
               'models' => $users->pluck('id')->toArray()
           ])
           ->assertStatus(Response::HTTP_NO_CONTENT);

        Notification::assertSentTo($users, ResetPasswordNotification::class);
    }

    /** @test */
    public function it_should_fail_if_not_authorized()
    {
        Notification::fake();
        collect([
            'users.activate',
            'users.deactivate',
            'users.delete',
        ])->each(function ($action) {
            $this->postJson(route('bulk-actions'), [
                'action' => $action,
                'models' => [1],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
        });

        Notification::assertNothingSent();
    }
}
