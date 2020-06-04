<?php

namespace App\Services\Apis;

use Exception;
use Illuminate\Support\Facades\Http;

class RipCms
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = config('services.ripcms.url');
        $this->token = config('services.ripcms.token');
    }

    public function getCompanies()
    {
        $response = Http::withToken($this->token)
            ->post("{$this->url}ProfitTools/GetCompanies");

        if ($response->failed()) {
            throw new Exception("RipCmsAPI ProfitTools/GetCompanies failed with message".$response->body());
        }

        return $response->json();
    }

    public function getCompany($id)
    {
        $response = Http::withToken($this->token)
            ->get("{$this->url}ProfitTools/GetCompany/{$id}");

        if ($response->failed()) {
            throw new Exception("RipCmsAPI /ProfitTools/GetCompany/{$id} failed with message".$response->body());
        }

        return $response->json();
    }
}
