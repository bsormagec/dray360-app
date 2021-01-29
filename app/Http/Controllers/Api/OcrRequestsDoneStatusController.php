<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToUpdateStatus;

class OcrRequestsDoneStatusController extends Controller
{
    public function __invoke(Request $request, $requestId)
    {
        $data = $request->validate(['done' => 'required|boolean']);
        $ocrRequest = OCRRequest::query()
            ->where('request_id', $requestId)
            ->whereNull('order_id')
            ->with('latestOcrRequestStatus')
            ->firstOrFail();
        $this->authorize('update', $ocrRequest);

        $ocrRequest->update(['done_at' => $data['done'] ? now() : null]);

        $snsData = [
            'request_id' => $ocrRequest->request_id,
            'company_id' => $ocrRequest->latestOcrRequestStatus->company_id,
            'status' => $data['done']
                ? OCRRequestStatus::REQUEST_MARKED_DONE
                : OCRRequestStatus::REQUEST_MARKED_UNDONE,
            'status_metadata' => ['user_id' => auth()->id()],
        ];
        $response = app(PublishSnsMessageToUpdateStatus::class)($snsData);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'data' => $data['done']
                ? 'Request marked as done successfully'
                : 'Request marked as undone successfully'
        ]);
    }
}
