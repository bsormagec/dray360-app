<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        \App\Models\OrderLineItem::class => \App\Policies\OrderLineItemPolicy::class,
        \App\Models\OrderAddressEvent::class => \App\Policies\OrderAddressEventPolicy::class,
        \App\Models\OCRRule::class => \App\Policies\OcrRulePolicy::class,
        \App\Models\OCRVariant::class => \App\Policies\OcrVariantPolicy::class,
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\CompanyOCRVariantOCRRule::class => \App\Policies\CompanyOcrVariantOcrRulePolicy::class,
        \App\Models\TMSProvider::class => \App\Policies\TmsProviderPolicy::class,
        \App\Models\VerifiedAddress::class => \App\Policies\VerifiedAddressPolicy::class,
        \App\Models\EquipmentType::class => \App\Policies\EquipmentTypePolicy::class,
        \App\Models\OCRRequest::class => \App\Policies\OcrRequestPolicy::class,
        \App\Models\OCRRequestStatus::class => \App\Policies\OcrRequestStatusPolicy::class,
        \App\Models\Address::class => \App\Policies\AddressPolicy::class,
        \App\Models\CompanyAddressTMSCode::class => \App\Policies\CompanyAddressTmsCodePolicy::class,
        \App\Models\DictionaryItem::class => \App\Policies\DictionaryItemPolicy::class,
        \OwenIt\Auditing\Models\Audit::class => \App\Policies\AuditLogPolicy::class,
        \App\Models\ObjectLock::class => \App\Policies\ObjectLockPolicy::class,
        \App\Models\FieldMap::class => \App\Policies\FieldMapPolicy::class,
        \App\Models\FeedbackComment::class => \App\Policies\FeedbackCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return $user && $user->isAbleTo('nova-view');
        });
    }
}
