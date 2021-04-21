<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
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
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->when(! auth()->user()->isAbleTo('all-companies-view'), function ($query) {
                return $query->forCurrentCompany()
                    ->where(function ($query) {
                        $query->whereDoesntHave('roles')
                            ->orWhereHas('roles', function ($query) {
                                $query->where('name', '!=', 'superadmin');
                            });
                    });
            })
            ->with([
                'company:id,name',
                'roles:id,name'
            ]);

        $users = QueryBuilder::for($query)
            ->allowedFilters(['name', 'email', 'id', AllowedFilter::scope('active')])
            ->allowedSorts(['name'])
            ->paginate($request->get('perPage', 10));

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
        $superadminRole = Role::where('name', 'superadmin')->first();
        $data = $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => [
                'required',
                'not_in:'. (! is_superadmin() ? $superadminRole->id : null),
                'exists:roles,id',
            ],
            'company_id' => 'nullable|exists:t_companies,id'
        ]);
        $roleId = $data['role_id'];
        $companyId = Company::find($data['company_id'] ?? null);
        $password = bcrypt($data['password']);
        unset($data['password']);
        unset($data['role_id']);
        unset($data['company_id']);

        $user = (new User($data))->setCompany($companyId ?: currentCompany());
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

        $user->load('company:id,name', 'roles:id,name');

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
        $superadminRole = Role::where('name', 'superadmin')->first();
        $data = $request->validate([
            'name' => 'sometimes|string|min:3',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignoreModel($user)],
            'position' => 'sometimes',
            'org' => 'sometimes',
            'role_id' => [
                'sometimes',
                'not_in:'.(! is_superadmin() ? $superadminRole->id : null),
                'exists:roles,id',
            ],
            'company_id' => 'nullable|exists:t_companies,id'
        ]);
        $roleId = $data['role_id'] ?? null;
        $companyId = $data['company_id'] ?? null;
        unset($data['role_id']);
        unset($data['company_id']);
        $data['t_company_id'] = $companyId ?: $user->getCompanyId();

        if ($roleId) {
            $user->syncRoles([$roleId]);
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
        $user->update([
            'original_email' => $user->email,
            'email' => null,
        ]);
        $user->delete();

        return response()->noContent();
    }
}
