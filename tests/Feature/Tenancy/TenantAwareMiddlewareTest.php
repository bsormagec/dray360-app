<?php

namespace Tests\Feature\Tenancy;

use Tests\TestCase;
use App\Models\User;
use App\Models\Domain;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\Seeds\UsersSeeder;
use Illuminate\Http\Response;
use App\Http\Middleware\TenantAware;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantAwareMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_identify_the_current_tenant_from_the_domain()
    {
        $domain = factory(Domain::class)->create();
        $tenancy = app('tenancy');
        $referer = Str::of($domain->hostname)
            ->start('http://')
            ->finish('/dashboard/asdfasdf/asdfads');

        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['Referer' => $referer])
        );

        $this->assertFalse($tenancy->isTenantSet());

        app(TenantAware::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($tenancy->isTenantSet());
        $this->assertEquals($domain->tenant->id, $tenancy->tenantId());
    }

    /** @test */
    public function it_should_throw_an_error_if_the_user_tries_to_access_the_web_from_other_domain()
    {
        $domain = factory(Domain::class)->create();
        $this->seed(UsersSeeder::class);
        $user = User::whereRoleIs('customer-user')->first();
        $user->setCompany(factory(Company::class)->create(['t_domain_id' => $domain->id]), true);
        $tenancy = app('tenancy');
        $previousEnv = $this->app['env']; // The middleware is disabled for test environment.
        $this->app['env'] = 'another';
        Sanctum::actingAs($user);

        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['Referer' => 'https://another.domain.com/somepage'])
        );

        $this->assertFalse($tenancy->isTenantSet());

        /** @var \Illuminate\Http\Response */
        $response = app(TenantAware::class)
            ->handle($request, function ($request) {
            });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->app['env'] = $previousEnv;
    }

    /** @test */
    public function it_should_allow_to_use_the_current_user_in_the_current_domain_if_impersonating()
    {
        $domain = factory(Domain::class)->create();
        $this->seed(UsersSeeder::class);
        $user = User::whereRoleIs('customer-user')->first();
        $user->setCompany(factory(Company::class)->create(['t_domain_id' => $domain->id]), true);
        $previousEnv = $this->app['env'];
        $this->app['env'] = 'another'; // The middleware is disabled for test environment.
        $this->loginAdmin();
        $this->app['impersonate']->startWith($user);
        Config::set('sanctum.stateful', [$user->company->domain->hostname, 'another.domain.com']);

        $this->getJson(
            route('user'),
            ['Referer' => 'https://another.domain.com/somepage']
        )
        ->assertJsonFragment(['name' => $user->name])
        ->assertStatus(Response::HTTP_OK);

        $this->assertTrue(app('tenancy')->isTenantSet());
        $this->assertEquals(app('tenancy')->tenantId(), $domain->tenant->id);
        $this->app['env'] = $previousEnv;
    }
}
