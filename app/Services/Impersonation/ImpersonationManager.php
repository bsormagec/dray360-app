<?php

namespace App\Services\Impersonation;

use App\Models\User;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Application;
use App\Exceptions\ImpersonationException;

class ImpersonationManager
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function startWith(User $userToImpersonate): void
    {
        throw_if($this->isImpersonating(), new ImpersonationException('There is an impersonating session already in place.'));

        $authGuard = $this->app['auth']->guard();

        $this->app['session']->put($this->getImpersonatedSessionKey(), $userToImpersonate->id);
        $this->app['session']->put($this->getImpersonatorSessionKey(), $authGuard->id());

        $this->setCurrentUser($authGuard, $userToImpersonate);
    }

    public function loadForRequest(): void
    {
        if (! $this->isImpersonating()) {
            return;
        }

        $toImpersonate = User::find($this->getImpersonatedId());
        $authGuard = $this->app['auth']->guard();

        $this->setCurrentUser($authGuard, $toImpersonate);
    }

    public function stop(): void
    {
        throw_if(! $this->isImpersonating(), new ImpersonationException('No impersonation session is currently set.'));

        $impersonator = User::find($this->getImpersonatorId());
        $authGuard = $this->app['auth']->guard();

        $this->app['session']->forget([
            $this->getImpersonatedSessionKey(),
            $this->getImpersonatorSessionKey(),
        ]);

        $this->setCurrentUser($authGuard, $impersonator, true);
    }

    protected function setCurrentUser($authGuard, User $user, bool $loginOnce = true): void
    {
        if ($authGuard instanceof RequestGuard) {
            $this->app['auth']->setUser($user);
        } else {
            $method = $loginOnce ? 'onceUsingId' : 'loginUsingId';
            $this->app['auth']->{$method}($user->id);
        }

        if ($user->hasCompany()) {
            $this->app['tenancy']->setCurrentCompanyFromId($user->getCompanyId());
        } else {
            $this->app['tenancy']->setCurrentCompany(null);
        }
    }

    public function isImpersonating(): bool
    {
        return $this->app['session']->has($this->getImpersonatedSessionKey())
            || $this->app['session']->has($this->getImpersonatorSessionKey());
    }

    public function getImpersonatorId()
    {
        return $this->app['session']->get($this->getImpersonatorSessionKey());
    }

    public function getImpersonatedId()
    {
        return $this->app['session']->get($this->getImpersonatedSessionKey());
    }

    public function getImpersonatedSessionKey(): string
    {
        return 'impersonated-user';
    }

    public function getImpersonatorSessionKey(): string
    {
        return 'impersonator-user';
    }
}
