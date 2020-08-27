<?php

namespace Tests\Feature\Tenancy;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Http\Middleware\IdentifyCurrentCompany;
use App\Services\Tenancy\CurrentCompanyManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IdentifyCurrentCompanyMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_identify_the_current_company_from_the_user_company()
    {
        $user = factory(User::class)->create(['t_company_id' => factory(Company::class)]);
        $companyManager = $this->app['company_manager'];
        Sanctum::actingAs($user);
        $request = Request::create('/test');

        $this->assertFalse($companyManager->isCompanySet());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($companyManager->isCompanySet());
        $this->assertEquals($user->t_company_id, $companyManager->companyId());
    }

    /** @test */
    public function it_should_identify_company_from_request_param()
    {
        $company = factory(Company::class, 5)->create()->get(2);
        $companyManager = $this->app['company_manager'];
        $request = Request::create('/test', 'GET', ['company_id' => "{$company->id}"]);

        $this->assertFalse($companyManager->isCompanySet());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($companyManager->isCompanySet());
        $this->assertEquals($company->id, $companyManager->companyId());
    }

    /** @test */
    public function it_should_not_fail_if_the_request_param_is_empty_null_or_undefined()
    {
        $company = factory(Company::class, 5)->create()->get(2);
        $companyManager = $this->app['company_manager'];
        $request = Request::create('/test', 'GET', ['company_id' => null]);

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertFalse($companyManager->isCompanySet());


        $request = Request::create('/test', 'GET', ['company_id' => 'undefined']);
        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertFalse($companyManager->isCompanySet());
    }

    /** @test */
    public function it_should_identify_company_from_request_headers()
    {
        $company = factory(Company::class, 5)->create()->get(2);
        $companyManager = $this->app['company_manager'];
        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars([CurrentCompanyManager::HEADER => $company->id])
        );

        $this->assertFalse($companyManager->isCompanySet());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($companyManager->isCompanySet());
        $this->assertEquals($company->id, $companyManager->companyId());
    }
}
