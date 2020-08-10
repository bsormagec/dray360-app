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
        $tenancy = $this->app['tenancy'];
        Sanctum::actingAs($user);
        $request = Request::create('/test');

        $this->assertFalse($tenancy->isSetCurrentCompany());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($tenancy->isSetCurrentCompany());
        $this->assertEquals($user->t_company_id, $tenancy->currentCompanyId());
    }

    /** @test */
    public function it_should_identify_company_from_request_param()
    {
        $company = factory(Company::class, 5)->create()->get(2);
        $tenancy = $this->app['tenancy'];
        $request = Request::create('/test', 'GET', ['company_id' => $company->id]);

        $this->assertFalse($tenancy->isSetCurrentCompany());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($tenancy->isSetCurrentCompany());
        $this->assertEquals($company->id, $tenancy->currentCompanyId());
    }

    /** @test */
    public function it_should_identify_company_from_request_headers()
    {
        $company = factory(Company::class, 5)->create()->get(2);
        $tenancy = $this->app['tenancy'];
        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars([CurrentCompanyManager::HEADER => $company->id])
        );

        $this->assertFalse($tenancy->isSetCurrentCompany());

        app(IdentifyCurrentCompany::class)
            ->handle($request, function ($request) {
            });

        $this->assertTrue($tenancy->isSetCurrentCompany());
        $this->assertEquals($company->id, $tenancy->currentCompanyId());
    }
}
