<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CurrentTenantControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_retrieve_the_current_tenant_configuration_including_user_and_company_customizations_if_available()
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

        $this->getJson(route('current-tenant'))
            ->assertJsonFragment([
                'logo1' => $default->configuration['logo1'],
                'logo2' => $default->configuration['logo2'],
                'name' => $default->configuration['name'],
                'extra' => $default->configuration['extra'],
            ]);

        Sanctum::actingAs($user);
        $this->getJson(route('current-tenant'), [
                'Referer' => $company->domain->hostname
            ])
            ->assertJsonFragment([
                'logo1' => $default->configuration['logo1'],
                'logo2' => $companyTenant->configuration['logo2'],
                'name' => $company->configuration['name'],
                'extra' => $user->configuration['extra'],
            ]);
    }
}
