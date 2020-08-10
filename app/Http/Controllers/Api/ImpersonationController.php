<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ImpersonationController extends Controller
{
    /**
     * Start impersonation session.
     */
    public function update(User $user)
    {
        Gate::authorize('impersonate', $user);

        app('impersonate')->startWith($user);

        return response()->json([
            'redirect' => '/dashboard',
        ]);
    }

    /**
     * Stop the impersonation session.
     */
    public function destroy()
    {
        app('impersonate')->stop();

        return response()->json([
            'redirect' => config('nova.path'),
        ]);
    }
}
