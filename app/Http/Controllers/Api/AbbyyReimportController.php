<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRRequest;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AbbyyReimportController extends Controller
{
    const REIMPORT_ABBYY_FUNCTION = 'reimportfromabbyy';

    public function __invoke($requestId)
    {
        OCRRequest::where('request_id', $requestId)->firstOrFail();

        $this->authorize('create', OCRRequestStatus::class);

        $response = Http::withHeaders([
                'X-API-KEY' => config('services.dray360-api.api_key')
            ])
            ->post(config('services.dray360-api.url'), [
                'function' => self::REIMPORT_ABBYY_FUNCTION,
                'request_id' => $requestId,
            ]);

        return response()->json([
            'message' => $response->body()
        ], $response->status());
    }
}
