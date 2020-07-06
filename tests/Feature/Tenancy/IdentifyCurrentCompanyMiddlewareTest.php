<?php

namespace Tests\Feature\Tenancy;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Contracts\CurrentCompany;
use App\Http\Middleware\IdentifyCurrentCompany;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IdentifyCurrentCompanyMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_identify_the_current_company_from_the_user_company()
    {
        $user = factory(User::class)->create(['t_company_id' => factory(Company::class)]);
        Sanctum::actingAs($user);

        $request = Request::create('/test');

        $this->assertFalse($this->app->bound(CurrentCompany::class));

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($this->app->bound(CurrentCompany::class));
        $this->assertEquals($user->t_company_id, app(CurrentCompany::class)->id);
    }

    /** @test */
    public function it_should_identify_company_from_request_param()
    {
        $company = factory(Company::class, 5)->create()->get(2);

        $request = Request::create('/test', 'GET', ['company_id' => $company->id]);

        $this->assertFalse($this->app->bound(CurrentCompany::class));

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($this->app->bound(CurrentCompany::class));
        $this->assertEquals($company->id, app(CurrentCompany::class)->id);
    }

    /** @test */
    public function it_should_identify_company_from_request_headers()
    {
        $company = factory(Company::class, 5)->create()->get(2);

        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars([IdentifyCurrentCompany::HEADER => $company->id])
        );

        $this->assertFalse($this->app->bound(CurrentCompany::class));

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($this->app->bound(CurrentCompany::class));
        $this->assertEquals($company->id, app(CurrentCompany::class)->id);
    }
}
