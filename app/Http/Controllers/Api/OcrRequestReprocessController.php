<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToReprocessRequest;

class OcrRequestReprocessController extends Controller
{
    public function __invoke($requestId)
    {
        $this->authorize('create', OCRRequestStatus::class);

        $status = OCRRequestStatus::query()
            ->where([
                'status' => OCRRequestStatus::OCR_COMPLETED,
                'request_id' => $requestId,
            ])
            ->firstOrFail();

        $response = app(PublishSnsMessageToReprocessRequest::class)($status);

        if ($response['status'] == 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }
}
