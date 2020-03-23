<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\User;
//use User;

class AuthenticationController extends Controller
{

    /**
     * Create new user account
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] name
     * @return [string] success message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string' // bad: 'password' => 'required|string|confirmed'

        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }


    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials))
        {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }
        $user = Auth::user();
        $tokenResult = $user->createToken('ApiPassToken');
        $token = $tokenResult->accessToken; // was: $token = $tokenResult->token;

        if ($request->remember_me)
        {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response()->json([
            'access_token' => $token->token,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->accessToken->expires_at //was: $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }


    /**
     * Logout user (Revoke the token)
     *
     * ASDF NOT WORKING?
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Get the currently authenticated User
     *
     * ASDF NOT WORKING?
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $authUser = $request->user();
        $user = User::with('roles.permissions')->where('id', '=', $authUser->id)->firstOrFail();
        return response()->json($user);
    }

}
