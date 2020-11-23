<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected User $customerAdmin;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginCustomerAdmin();
        $this->customerAdmin = auth()->user();
    }

    /** @test */
    public function it_should_list_all_the_users_of_the_current_admin_company()
    {
        factory(User::class, 5)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);
        factory(User::class, 5)->create([Company::FOREIGN_KEY => factory(Company::class)->create()]);

        $this->getJson(route('users.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'company' => ['id', 'name'],
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(6, 'data');
    }

    /** @test */
    public function it_should_allow_to_filter_by_active_and_and_inactive_users()
    {
        $active = factory(User::class)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);
        $inactive = factory(User::class)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);
        $inactive->deactivate();

        $this->getJson(route('users.index', ['filter[active]' => true]))
            ->assertJsonCount(2, 'data');

        $this->getJson(route('users.index', ['filter[active]' => false]))
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_should_fail_if_not_authorized()
    {
        $this->loginNoAdmin();

        $this->getJson(route('users.index'))->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_show_the_details_of_the_requested_user()
    {
        $user = factory(User::class)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);

        $this->getJson(route('users.show', $user->id))
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'company' => ['id', 'name'],
            ]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_view_user()
    {
        $user = factory(User::class)->create([Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()]);
        $this->loginNoAdmin();

        $this->getJson(route('users.show', $user->id))->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_create_a_new_user()
    {
        $role = Role::where('name', '!=', 'superadmin')->first();
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testtest',
            'role_id' => $role->id,
        ];

        $this->withoutExceptionHandling();
        $this->postJson(route('users.store'), $user)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['name' => $user['name']]);

        $user = User::where('email', $user['email'])->first();
        $this->assertEquals($this->customerAdmin->getCompanyId(), $user->getCompanyId());
        $this->assertTrue(Hash::check('testtest', $user->password));
        $this->assertTrue($user->hasRole($role->name));
    }

    /** @test */
    public function it_should_not_allow_to_create_a_superadmin()
    {
        $role = Role::where('name', 'superadmin')->first();
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testtest',
            'role_id' => $role->id,
        ];

        $this->postJson(route('users.store'), $user)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['role_id']);

        $this->assertDatabaseMissing('users', ['email' => $user['email']]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_create_users()
    {
        $this->loginNoAdmin();
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testtest',
        ];

        $this->postJson(route('users.store'), $user)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_update_a_user()
    {
        $role = Role::where('name', '!=', 'superadmin')->first();
        $user = factory(User::class)->create();
        $user->setCompany($this->customerAdmin->company)->save();
        $user->attachRole($role);
        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'role_id' => $role->id,
            'position' => $this->faker->jobTitle,
            'org' => $this->faker->company
        ];

        $this->putJson(route('users.update', $user->id), $newData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['name' => $newData['name']]);

        $user->refresh();
        $this->assertEquals($this->customerAdmin->getCompanyId(), $user->getCompanyId());
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertTrue($user->hasRole($role->name));
    }

    /** @test */
    public function it_should_not_let_update_to_a_superadmin()
    {
        $role = Role::where('name', 'superadmin')->first();
        $user = factory(User::class)->create();
        $user->setCompany($this->customerAdmin->company)->save();
        $user->attachRole(Role::where('name', '!=', 'superadmin')->first());
        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'role_id' => $role->id,
            'position' => $this->faker->jobTitle,
            'org' => $this->faker->company
        ];

        $this->putJson(route('users.update', $user->id), $newData)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['role_id']);

        $user->refresh();
        $this->assertFalse($user->hasRole($role->name));
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_update_users()
    {
        $this->loginNoAdmin();
        $user = factory(User::class)->create();

        $this->putJson(route('users.update', $user->id), [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_delete_a_user()
    {
        $user = factory(User::class)->create([
            Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()
        ]);

        $this->deleteJson(route('users.destroy', $user->id))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_delete_variants()
    {
        $this->loginNoAdmin();
        $user = factory(User::class)->create([
            Company::FOREIGN_KEY => $this->customerAdmin->getCompanyId()
        ]);

        $this->deleteJson(route('users.destroy', $user->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
