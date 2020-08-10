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
        $tenancy = $this->app['tenancy'];
        currentCompany($company);

        $this->assertTrue($tenancy->isSetCurrentCompany());
        $this->assertEquals($company->id, currentCompany()->id);
    }
}
