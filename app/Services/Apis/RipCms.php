<?php

namespace App\Services\Apis;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RipCms
{
    const TOKEN_CACHE_KEY = 'rip-cms-cache-key';
    protected $url;
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $token;

    public function __construct()
    {
        $this->url = config('services.ripcms.url');
        $this->username = config('services.ripcms.username');
        $this->password = config('services.ripcms.password');
        $this->apiUrl = "{$this->url}api/";
        $this->token = Cache::get(self::TOKEN_CACHE_KEY);
    }

    public function getToken(): self
    {
        if (Cache::has(self::TOKEN_CACHE_KEY)) {
            $this->token = Cache::get(self::TOKEN_CACHE_KEY);
            return $this;
        }

        $response = Http::asForm()
            ->post("{$this->url}token", [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ]);

        if ($response->failed()) {
            throw new Exception("RipCmsAPI ProfitTools/GetToken failed with message".$response->body());
        }

        $this->token = $response['access_token'];
        Cache::put(self::TOKEN_CACHE_KEY, $this->token, now()->addHour());

        return $this;
    }

    public function getCompanies()
    {
        $response = Http::withToken($this->token)
            ->post("{$this->apiUrl}ProfitTools/GetCompanies");

        if ($response->failed()) {
            throw new Exception("RipCmsAPI ProfitTools/GetCompanies failed with message".$response->body());
        }

        return $response->json();
    }

    public function getCompany($id)
    {
        $response = Http::withToken($this->token)
            ->get("{$this->apiUrl}ProfitTools/GetCompany/{$id}");

        if ($response->failed()) {
            throw new Exception("RipCmsAPI /ProfitTools/GetCompany/{$id} failed with message".$response->body());
        }

        return $response->json();
    }
}
