<?php

namespace App\Actions\Bulk;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ResetPasssword
{
    use SendsPasswordResetEmails;

    public function __invoke(Request $data, array $ids)
    {
        $users = User::whereIn('id', $ids)->get(['email']);

        $users->each(function ($user) {
            $this->broker()->sendResetLink(['email' => $user->email]);
        });

        return response()->noContent();
    }
}
