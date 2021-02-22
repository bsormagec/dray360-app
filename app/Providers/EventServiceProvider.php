<?php

namespace App\Providers;

use App\Events\AddressVerified;
use App\Events\AttributeVerified;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\CreateOrUpdateCacheEntry;
use App\Listeners\CreateOrUpdateVerifiedAddressEntry;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AddressVerified::class => [
            CreateOrUpdateVerifiedAddressEntry::class
        ],
        AttributeVerified::class => [
            CreateOrUpdateCacheEntry::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
