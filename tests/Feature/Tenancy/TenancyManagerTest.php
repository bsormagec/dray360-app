<?php

namespace Tests\Feature\Tenancy;

use UsersSeeder;
use Tests\TestCase;
use App\Models\User;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenancyManagerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_identify_the_current_tenant_from_the_domain()
    {
        $tenant = factory(Tenant::class)->create();
        $domain = factory(Domain::class)->create(['t_tenant_id' => $tenant->id]);
        $referer = Str::of($domain->hostname)
            ->start('http://')
            ->finish(':8080/dashboard/another/path');

        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['Referer' => $referer])
        );

        app('tenancy')->initialize($request);

        $this->assertEquals($tenant->id, tenant()->id);
    }

    /** @test */
    public function it_should_identify_if_the_current_user_is_using_the_right_domain()
    {
        $domain = factory(Domain::class)->create();
        $this->seed(UsersSeeder::class);
        $user = User::whereRoleIs('customer-user')->first();
        $user->setCompany(factory(Company::class)->create(['t_domain_id' => $domain->id]), true);
        $tenancy = app('tenancy');

        $request = Request::create(
            '/test',
            'GET',
            [],
            [],
            [],
            $this->transformHeadersToServerVars(['Referer' => 'https://another.domain.com/somepage'])
        );

        $this->assertFalse($tenancy->isUsingRightDomain($request, $user));
    }

    /** @test */
    public function it_should_merge_the_configuration_from_the_base_tenant_with_the_rest_of_the_current_tenant_company_and_user_configuration()
    {
        $this->seedTestUsers();
        $default = Tenant::getDefaultTenant();
        $default->configuration = [
            'logo1' => 'something',
            'logo2' => 'another',
            'name' => 'the name',
            'extra' => 'the extra',
        ];
        $default->save();
        $company = factory(Company::class)->create([
            'configuration' => ['name' => 'company changed'],
        ]);
        $companyTenant = $company->domain->tenant;
        $companyTenant->update([
            'configuration' => ['logo2' => 'tenant changed'],
        ]);
        $user = User::whereRoleIs('customer-user')->first();
        $user
            ->setCompany($company)
            ->setAttribute('configuration', ['extra' => 'user changed'])
            ->save();

        $finalConfiguration = app('tenancy')
            ->setTenant($companyTenant)
            ->getConfiguration($user);

        $this->assertEquals($finalConfiguration['logo1'], $default->configuration['logo1']);
        $this->assertEquals($finalConfiguration['logo2'], $companyTenant->configuration['logo2']);
        $this->assertEquals($finalConfiguration['name'], $company->configuration['name']);
        $this->assertEquals($finalConfiguration['extra'], $user->configuration['extra']);
    }

    /** @test */
    public function it_should_return_the_configuration_value_from_the_whole_tenant_company_user_configuration()
    {
        $this->seedTestUsers();
        $default = Tenant::getDefaultTenant();
        $default->configuration = [
            'logo1' => 'something',
            'logo2' => 'another',
            'name' => 'the name',
            'extra' => 'the extra',
        ];
        $default->save();
        $company = factory(Company::class)->create([
            'configuration' => ['name' => 'company changed'],
        ]);
        $companyTenant = $company->domain->tenant;
        $companyTenant->update([
            'configuration' => ['logo2' => 'tenant changed'],
        ]);
        $user = User::whereRoleIs('customer-user')->first();
        $user
            ->setCompany($company)
            ->setAttribute('configuration', ['extra' => 'user changed'])
            ->save();

        $tenancy = app('tenancy')
            ->setTenant($companyTenant)
            ->loadConfiguration($user);

        $this->assertEquals($default->configuration['logo1'], $tenancy->getConfigurationValue('logo1'));
        $this->assertEquals($companyTenant->configuration['logo2'], $tenancy->getConfigurationValue('logo2'));
        $this->assertEquals($company->configuration['name'], $tenancy->getConfigurationValue('name'));
        $this->assertEquals($user->configuration['extra'], $tenancy->getConfigurationValue('extra'));
    }
}
