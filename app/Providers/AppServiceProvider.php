<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Services\Tenancy\TenancyManager;
use App\Services\Tenancy\CurrentCompanyManager;
use App\Services\Impersonation\ImpersonationManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton('impersonate', function ($app) {
            return new ImpersonationManager($app);
        });

        $this->app->singleton('company_manager', function ($app) {
            return new CurrentCompanyManager($app);
        });

        $this->app->singleton('tenancy', function ($app) {
            return new TenancyManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('impersonate', function (User $user, $userToImpersonate) {
            return $user->isAbleTo('users-impersonate');
        });
    }
}
