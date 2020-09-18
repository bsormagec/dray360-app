<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->forCurrentCompany()
            ->with([
                'company:id,name',
                'roles:id,name'
            ]);

        $users = QueryBuilder::for($query)
            ->allowedFilters(['name', 'email', 'id', AllowedFilter::scope('active')])
            ->paginate(10);

        return JsonResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);
        $roleId = $data['role_id'];
        $password = bcrypt($data['password']);
        unset($data['password']);
        unset($data['role_id']);

        $user = (new User($data))->setCompany(currentCompany());
        $user->password = $password;
        $user->save();
        $user->attachRole($roleId);

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('company:id,name');

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->validate([
            'name' => 'sometimes|string|min:3',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignoreModel($user)],
            'position' => 'sometimes',
            'org' => 'sometimes',
            'role_id' => 'sometimes|exists:roles,id',
        ]);
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);
        if ($roleId) {
            $user->syncRolesWithoutDetaching([$roleId]);
        }
        $user->update($data);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->noContent();
    }
}
