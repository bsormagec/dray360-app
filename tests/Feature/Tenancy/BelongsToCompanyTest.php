<?php

namespace Tests\Feature\Tenancy;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BelongsToCompanyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_allow_filtering_by_related_tenant()
    {
        $company = factory(Company::class)->create();
        factory(User::class, 5)->create([Company::FOREIGN_KEY => $company->id]);
        factory(User::class, 5)->create();

        currentCompany($company);

        $this->assertCount(5, User::forCurrentCompany()->get());
    }

    /** @test */
    public function it_should_have_the_company_relationship()
    {
        $company = factory(Company::class)->create();
        $user = factory(User::class)->create([Company::FOREIGN_KEY => $company->id]);

        $this->assertEquals($company->id, $user->company()->first()->id);
    }
}
