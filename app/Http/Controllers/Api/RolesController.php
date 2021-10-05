<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class RolesController extends Controller
{
    public function __invoke()
    {
        return JsonResource::collection(
            Role::query()
                ->orderBy('display_name')
                ->when(! is_superadmin(), function ($query) {
                    return $query->where('name', '!=', 'superadmin');
                })
                ->get(['id', 'display_name', 'name'])
        );
    }
}
