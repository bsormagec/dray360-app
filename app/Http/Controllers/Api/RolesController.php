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
            Role::where('name', '!=', 'superadmin')->get(['id', 'display_name'])
        );
    }
}
