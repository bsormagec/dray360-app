<?php

namespace App\Actions\Bulk;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeactivateUsers
{
    public function __invoke(Request $data, array $ids)
    {
        Gate::authorize('bulkUpdate', [User::class, $ids]);
        User::bulkDeactivate($ids);

        return response()->noContent();
    }
}
