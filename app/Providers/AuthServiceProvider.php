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
