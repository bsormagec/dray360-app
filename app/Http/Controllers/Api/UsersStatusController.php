<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersStatusController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $request->validate(['active' => 'required|boolean']);

        if (! $request->boolean('active')) {
            $user->deactivate();
        } else {
            $user->activate();
        }

        return response()->json($user);
    }
}
