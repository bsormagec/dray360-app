<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ImpersonationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

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

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => trans('auth.failed')], 401);
        }

        $user = auth()->user();

        if (! $user->isActive() || ! $user->company->isActive()) {
            Auth::guard('web')->logout();
            return response()->json(['message' => 'This user is not active in the system'], 401);
        }

        if (! app('tenancy')->isUsingRightDomain($request, $user)) {
            Auth::guard('web')->logout();
            return app('tenancy')->getRedirectErrorResponse($user);
        }

        return response()->noContent();
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            app('impersonate')->stop();
        } catch (ImpersonationException $e) {
        }
        Auth::guard('web')->logout();

        return response()->noContent();
    }

    /**
     * Get the currently authenticated User
     */
    public function user(Request $request)
    {
        $user = $request->user();
        if (! is_object($user) || ! $user->isActive()) {
            return response()->json(['message' => 'Not authorized'], 401);
        } else {
            $userData = $user->load('company')
                ->setRelation('permissions', $user->allPermissions())
                ->toArray();
            $userData['is_superadmin'] = $user->isSuperadmin();
            $userData['is_impersonated'] = app('impersonate')->isImpersonating();
            $userData['configuration'] = app('tenancy')->getConfiguration($user);

            return response()->json($userData);
        }
    }
}
