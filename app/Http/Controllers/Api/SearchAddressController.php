<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SearchAddressController extends Controller
{
    public function __invoke(Request $request)
    {
        $config = config('services.search-address');

        $response = Http::withHeaders(['X-API-KEY' => $config['api_key']])
            ->post($config['url'], $request->all());

        if ($response->failed()) {
            return response()
                ->json(['message' => $response->body()], $response->status());
        }

        return response()->json($response->json());
    }
}
