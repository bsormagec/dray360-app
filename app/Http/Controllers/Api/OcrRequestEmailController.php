<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRRequest;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;

class OcrRequestEmailController extends Controller
{
    public function __invoke($requestId)
    {
        $this->authorize('viewAny', OCRRequest::class);

        $status = OCRRequestStatus::query()
            ->whereIn('status', [OCRRequestStatus::INTAKE_STARTED, OCRRequestStatus::INTAKE_REJECTED])
            ->where('request_id', $requestId)
            ->orderByDesc('id')
            ->firstOrFail();

        return response()->json(['data' => $status->status_metadata]);
    }
}
