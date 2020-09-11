<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChangePasswordControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_change_the_user_password()
    {
        $this->loginCustomerAdmin();
        $user = auth()->user();
        $oldPassword = 'testtest';
        $newPassword = 'newpassword';
        $user->password = bcrypt($oldPassword);

        $this->postJson(route('password.change'), [
            'old_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertStatus(Response::HTTP_NO_CONTENT);

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
        $this->assertFalse(Hash::check($oldPassword, $user->password));
    }

    /** @test */
    public function it_should_fail_if_the_old_password_doesnt_match()
    {
        $this->loginCustomerAdmin();
        $user = auth()->user();
        $newPassword = 'newpassword';
        $user->password = bcrypt('testtest');

        $this->postJson(route('password.change'), [
            'old_password' => 'doesntmatch',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors('old_password');
    }
}
