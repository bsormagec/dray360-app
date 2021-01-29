<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToUpdateStatus;

class OcrRequestReprocessController extends Controller
{
    public function __invoke($requestId)
    {
        $this->authorize('create', OCRRequestStatus::class);

        $status = OCRRequestStatus::query()
            ->whereIn('status', [OCRRequestStatus::OCR_COMPLETED, OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE])
            ->where('request_id', $requestId)
            ->firstOrFail();

        $data = [
            'request_id' => $status->request_id,
            'status' => $status->status === OCRRequestStatus::OCR_COMPLETED
                ? OCRRequestStatus::OCR_COMPLETED
                : OCRRequestStatus::INTAKE_ACCEPTED_DATAFILE,
            'status_metadata' => $status->status_metadata,
            'company_id' => $status->company_id,
        ];

        $response = app(PublishSnsMessageToUpdateStatus::class)($data);

        if ($response['status'] == 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }
}
