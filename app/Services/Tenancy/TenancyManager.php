<?php

namespace App\Services\Tenancy;

use App\Models\User;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;

class TenancyManager
{
    protected Application $app;
    protected ?Tenant $tenant = null;
    protected Collection $configuration;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->configuration = collect();
    }

    public function initialize(Request $request): self
    {
        $this->discoverTenantFromDomain($request);

        return $this;
    }

    public function loadTenantFromUser(User $user): self
    {
        if (! $user->hasCompany()) {
            return $this;
        }

        $tenant = optional($user->company)->domain->tenant ?? Tenant::getDefaultTenant();

        return $this->setTenant($tenant);
    }

    public function isUsingRightDomain(Request $request, User $user): bool
    {
        if (is_superadmin()) {
            return true;
        }

        if (! $user->hasCompany() || ! ($user->company->domain ?? null)) {
            return true;
        }

        $requestHostname = $this->getRefererHostname($request);
        $userHostname = $user->company->domain->hostname;

        return Str::is("$requestHostname*", $userHostname);
    }

    public function getRedirectErrorResponse(User $user): Response
    {
        return response()->json([
            'message' => 'Your company is not registered under this domain',
            'redirect' => $user->company->domain->hostname,
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param \App\Models\Company|\App\Models\User $model
     */
    public function loadConfiguration($model = null): self
    {
        if ($model && ! in_array(get_class($model), [User::class, Company::class])) {
            throw new InvalidArgumentException(get_class($model)." Received: Tenant configuration can only be loaded for users and companies");
        }

        $this->configuration = collect(Tenant::getDefaultTenant()->configuration ?? [])
            ->merge($this->tenant->configuration ?? [])
            ->when($model instanceof User, function ($config) use ($model) {
                return $config
                    ->when($model->hasCompany(), fn ($config) => $config->merge($model->company->configuration ?? []))
                    ->merge($model->configuration ?? []);
            })
            ->when($model instanceof Company, fn ($config) => $config->merge($model->configuration ?? []));

        return $this;
    }

    public function getConfigurationValue(string $key, $default = null)
    {
        return $this->configuration->get($key, $default);
    }

    /**
     * @param \App\Models\Company|\App\Models\User|null $model
     */
    public function getConfiguration($model = null): array
    {
        $this->loadConfiguration($model);

        return $this->configuration->toArray();
    }

    protected function discoverTenantFromDomain(Request $request): self
    {
        $hostname = $this->getRefererHostname($request);
        $domain = Domain::where(['hostname' => $hostname])->first();

        if (! $domain) {
            $this->setTenant(Tenant::getDefaultTenant());
            return $this;
        }

        $this->setTenant($domain->tenant);
        return $this;
    }

    protected function getRefererHostname(Request $request)
    {
        return Str::of($request->headers->get('referer'))
        ->replaceFirst('https://', '')
        ->replaceFirst('http://', '')
        ->finish('/')
        ->before('/')
        ->before(':');
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function isTenantSet(): bool
    {
        return $this->tenant != null;
    }

    public function tenantId(): ?int
    {
        return $this->tenant->id ?? null;
    }
}
