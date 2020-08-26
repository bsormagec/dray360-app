<?php

namespace Tests\Feature\Tenancy;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SetCurrentCompanyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_allow_setting_the_company_into_the_container()
    {
        $company = factory(Company::class)->create();
        $companyManager = $this->app['company_manager'];
        currentCompany($company);

        $this->assertTrue($companyManager->isCompanySet());
        $this->assertEquals($company->id, currentCompany()->id);
    }
}
