<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Order::class => \App\Policies\OrdersPolicy::class,
        \App\Models\OCRRule::class => \App\Policies\OcrRulePolicy::class,
        \App\Models\OCRVariant::class => \App\Policies\OcrVariantPolicy::class,
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\CompanyOCRVariantOCRRule::class => \App\Policies\CompanyOcrVariantOcrRulePolicy::class,
        \App\Models\TMSProvider::class => \App\Policies\TmsProviderPolicy::class,
        \App\Models\VerifiedAddress::class => \App\Policies\VerifiedAddressPolicy::class,
        \App\Models\EquipmentType::class => \App\Policies\EquipmentTypePolicy::class,
        \App\Models\OCRRequest::class => \App\Policies\OcrRequestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
