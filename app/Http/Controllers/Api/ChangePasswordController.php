<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = auth()->user();

        if (! Hash::check($data['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => 'old password does not match with current password',
            ]);
        }

        $user->password = bcrypt($data['password']);
        $user->save();

        return response()->noContent();
    }
}
