<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class AuthenticationController extends Controller
{

    /**
     * Create new user account
     *
     * @param  [Request] $request
     * @param  [string] $request->email
     * @param  [string] $request->password
     * @param  [string] $request->name
     * @return [string] success message
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
     *
     * @param  [Request] $request
     * @param  [string] $request->email
     * @param  [string] $request->password
     * @return [response] success or failure message+code
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
     *
     * @return [response] success or failure message+code
     */
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged Out'], 200);
    }


    /**
     * Get the currently authenticated User
     *
     * @param  [Request] $request
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $authUser = $request->user();
        if (!is_object($authUser)) {
            return response()->json(['message' => 'Not authorized'], 401);
        } else {
            $user = User::with('roles.permissions')->where('id', '=', $authUser->id)->firstOrFail();
            return response()->json($user);
        }
    }

}
