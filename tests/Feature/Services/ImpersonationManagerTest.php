<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Exceptions\ImpersonationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImpersonationManagerTest extends TestCase
{
    use DatabaseTransactions;

    protected $userToImpersonate;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();

        $this->userToImpersonate = factory(User::class)
            ->create(['t_company_id' => factory(Company::class)]);
        $this->userToImpersonate->attachRole('customer-user');
    }

    /** @test */
    public function it_should_allow_to_impersonate_a_user()
    {
        $admin = auth()->user();

        $this->assertNull(currentCompany());
        app('impersonate')->startWith($this->userToImpersonate);

        $this->assertTrue(session()->has(app('impersonate')->getImpersonatedSessionKey()));
        $this->assertTrue(session()->has(app('impersonate')->getImpersonatorSessionKey()));
        $this->assertEquals($this->userToImpersonate->id, auth()->user()->id);
        $this->assertEquals($admin->id, app('impersonate')->getImpersonatorId());
        $this->assertEquals($this->userToImpersonate->getCompanyId(), currentCompany()->id);
    }

    /** @test */
    public function it_checks_if_we_are_currently_impersonating()
    {
        $this->assertFalse(app('impersonate')->isImpersonating());

        app('impersonate')->startWith($this->userToImpersonate);

        $this->assertTrue(app('impersonate')->isImpersonating());
    }

    /** @test */
    public function it_should_stop_the_impersonation_and_return_session_back_to_normal()
    {
        $admin = auth()->user();
        app('impersonate')->startWith($this->userToImpersonate);
        app('impersonate')->stop();

        $this->assertFalse(app('impersonate')->isImpersonating());
        $this->assertEquals($admin->id, auth()->user()->id);
    }

    /** @test */
    public function it_throws_an_exception_if_no_impersionation_is_being_made()
    {
        $this->expectException(ImpersonationException::class);
        app('impersonate')->stop();
    }

    /** @test */
    public function it_should_fail_if_trying_to_impersonate_again_while_impersonating()
    {
        $this->expectException(ImpersonationException::class);

        app('impersonate')->startWith($this->userToImpersonate);
        app('impersonate')->startWith($this->userToImpersonate);
    }

    /** @test */
    public function it_should_load_the_impersonation_from_the_current_sesssion_only_for_current_request_and_update_the_current_company_and_tenant()
    {
        $admin = auth()->user();
        $tenant = $this->userToImpersonate->company->domain->tenant;
        session()->put(app('impersonate')->getImpersonatedSessionKey(), $this->userToImpersonate->id);
        session()->put(app('impersonate')->getImpersonatorSessionKey(), $admin->id);

        $this->assertNull(currentCompany());
        app('impersonate')->loadForRequest();

        $this->assertEquals($this->userToImpersonate->id, auth()->user()->id);
        $this->assertEquals($this->userToImpersonate->getCompanyId(), currentCompany()->id);
        $this->assertTrue(app('tenancy')->isTenantSet());
        $this->assertEquals($tenant->id, app('tenancy')->tenantId());
    }
}
