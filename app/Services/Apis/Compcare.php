<?php

namespace App\Services\Apis;

use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Exceptions\CompcareException;
use Illuminate\Support\Facades\Cache;

class Compcare
{
    protected string $identityUrl;
    protected string $entitiesUrl;
    protected string $ordersUrl;

    protected $apiKey;
    protected $token;
    protected Company $company;

    public function __construct(Company $company)
    {
        $this->identityUrl = Str::finish(config('services.compcare.identity_url'), '/api/');
        $this->entitiesUrl = Str::finish(config('services.compcare.entities_url'), '/api/');
        $this->ordersUrl = Str::finish(config('services.compcare.orders_url'), '/api/');

        $this->company = $company;
        $this->token = Cache::get(self::getTokenCacheKeyFor($this->company));

        $this->apiKey = $company->compcare_api_key;
    }

    public function getToken(): self
    {
        if (Cache::has(self::getTokenCacheKeyFor($this->company))) {
            $this->token = Cache::get(self::getTokenCacheKeyFor($this->company));
            return $this;
        }

        $response = Http::asJson()->get("{$this->identityUrl}Auth/apikey/{$this->apiKey}");

        if ($response->failed() || ! $response->json()) {
            throw new CompcareException('Auth/apikey', $response->body(), $response->status());
        }

        $this->token = Arr::get($response, 'data.token');
        Cache::put(self::getTokenCacheKeyFor($this->company), $this->token, now()->addHour());

        return $this;
    }

    public function getAddresses(int $page, int $limit): array
    {
        $response = Http::withToken($this->token)
            ->get(
                "{$this->entitiesUrl}Addresses/GetAddresses",
                ['page' => $page, 'limit' => $limit]
            );

        if ($response->failed() || ! is_array($response->json()) || $response['success'] == false) {
            throw new CompcareException('Addresses/GetAddresses', $response->body(), $response->status());
        }

        return $response->json();
    }

    public function getAllAddresses(): array
    {
        $page = 1;
        $limit = 100;
        $response = $this->getAddresses($page, $limit);
        $data = [];

        while (count($response['data']) !== 0) {
            $data = array_merge($data, $response['data']);
            $response = $this->getAddresses(++$page, $limit);
        }

        return $data;
    }

    public function getLoadTypes(): array
    {
        $response = Http::withToken($this->token)
            ->get("{$this->ordersUrl}LoadTypes/GetLoadTypes");

        if ($response->failed() || ! is_array($response->json()) || $response['success'] == false) {
            throw new CompcareException('LoadTypes/GetLoadTypes', $response->body(), $response->status());
        }

        return $response->json();
    }

    public static function getTokenCacheKeyFor(Company $company): string
    {
        return "compcare-token-for-".Str::kebab($company->name);
    }
}
