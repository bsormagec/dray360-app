<?php

namespace App\Actions\Bulk;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeleteUsers
{
    public function __invoke(Request $data, array $ids)
    {
        Gate::authorize('bulkDelete', [User::class, $ids]);
        User::whereIn('id', $ids)->delete();

        return response()->noContent();
    }
}
