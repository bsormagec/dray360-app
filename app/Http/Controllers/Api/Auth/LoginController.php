<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Create new user account
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->save();

        return response()->json(['message' => 'Successfully created user!'], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return response()->json(['message' => 'Login successful'], 200);
        } else {
            return response()->json(['message' => 'Not authorized'], 401);
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Logged Out'], 200);
    }

    /**
     * Get the currently authenticated User
     */
    public function user(Request $request)
    {
        $user = $request->user();
        if (! is_object($user)) {
            return response()->json(['message' => 'Not authorized'], 401);
        } else {
            $user->setRelation('permissions', $user->allPermissions());
            $user->is_superadmin = $user->isSuperadmin();
            return response()->json($user);
        }
    }
}
