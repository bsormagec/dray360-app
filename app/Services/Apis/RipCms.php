<?php

namespace App\Services\Apis;

use App\Models\Company;
use Illuminate\Support\Str;
use App\Exceptions\RipCmsException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RipCms
{
    protected $url;
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $token;
    protected Company $company;

    public function __construct(Company $company)
    {
        $this->url = config('services.ripcms.url');
        $this->company = $company;
        $this->apiUrl = "{$this->url}api/";
        $this->token = Cache::get(self::getTokenCacheKeyFor($this->company));
        $this->setupCredentials();
    }

    public function getToken(): self
    {
        if (Cache::has(self::getTokenCacheKeyFor($this->company))) {
            $this->token = Cache::get(self::getTokenCacheKeyFor($this->company));
            return $this;
        }

        $configToken = config('services.ripcms.'.Str::snake($this->company->name).'.token');
        $response = Http::asForm()
            ->post("{$this->url}token", [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ]);

        if (! $configToken && ($response->failed() || ! $response->json())) {
            throw new RipCmsException('GetToken', $response->body(), $response->status());
        }

        $this->token = $configToken ?? $response['access_token'];//use the token from the env otherwise use the one from the response.
        Cache::put(self::getTokenCacheKeyFor($this->company), $this->token, now()->addHour());

        return $this;
    }

    public function getCompanies()
    {
        $response = Http::withToken($this->token)
            ->get("{$this->apiUrl}ProfitTools/GetCompanies");

        if ($response->failed() || ! is_array($response->json())) {
            throw new RipCmsException('GetCompanies', $response->body(), $response->status());
        }

        return $response->json();
    }

    public function getCompany($id)
    {
        $response = Http::withToken($this->token)
            ->get("{$this->apiUrl}ProfitTools/GetCompany/{$id}");

        if ($response->failed() || ! is_array($response->json())) {
            throw new RipCmsException("GetCompany/{$id}", $response->body(), $response->status());
        }

        return $response->json();
    }

    protected function setupCredentials(): void
    {
        $credentials = config('services.ripcms.'.Str::snake($this->company->name));

        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
    }

    public static function getTokenCacheKeyFor(Company $company): string
    {
        return "rips-cms-token-for-".Str::kebab($company->name);
    }
}
