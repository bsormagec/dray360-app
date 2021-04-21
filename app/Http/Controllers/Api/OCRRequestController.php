<?php

namespace App\Http\Controllers\Api;

use App\Models\ObjectLock;
use App\Models\OCRRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Queries\OcrRequestsListQuery;
use App\Http\Resources\OcrRequestJson;
use App\Actions\GenerateUploadPresignedUrl;
use Illuminate\Validation\ValidationException;

class OCRRequestController extends Controller
{
    // only these image file extension types are accepted
    const VALID_FILE_TYPES = ['jpeg', 'png', 'jpg', 'tif', 'tiff', 'bmp', 'pdf', 'csv', 'xlsx', 'edi'];

    public function index(Request $request)
    {
        $this->authorize('viewAny', OCRRequest::class);

        $ocrRequests = (new OcrRequestsListQuery($request->get('selected')))->paginate(25);

        return new OcrRequestJson($ocrRequests);
    }

    public function store(Request $request)
    {
        // validate that filename parameter was provided
        $request->validate(['filename' => 'required|string']);
        $originalFilename = $request->filename;
        $extension = strtolower((new \SplFileInfo($originalFilename))->getExtension());

        if (! in_array($extension, self::VALID_FILE_TYPES)) {
            $extensionList = implode(',', self::VALID_FILE_TYPES);
            throw ValidationException::withMessages([
                'filename' => "Invalid file extension '{$extension}'. Must be one of: '{$extensionList}'",
            ]);
        }

        if (! currentCompany()) {
            throw ValidationException::withMessages([
                'company' => 'User not associated with a company can\'t upload files',
            ]);
        }

        $requestId = Str::uuid()->toString();
        $response = app(GenerateUploadPresignedUrl::class)([
            'request_id' => $requestId,
            'type' => ObjectLock::REQUEST_OBJECT,
            'filename' => $originalFilename,
        ]);

        if ($response['status'] === 'error') {
            return response()->json($response['data'], 500);
        }

        $responseData = $response['data'] + [
            'request_id' => $requestId,
            'original_filename' => $originalFilename,
            'company_id' => currentCompany()->id ?? null,
            'user_id' => auth()->user()->id,
            'variant_name' => $request->get('variant_name'),
            'datetime_utciso' => now()->toISOString(),
        ];

        OCRRequestStatus::createUploadRequest($responseData);

        return response()->json($responseData, 200);
    }

    public function destroy($ocrRequest)
    {
        $ocrRequest = OCRRequest::query()
            ->whereNull('order_id')
            ->where('request_id', $ocrRequest)
            ->firstOrFail();

        $this->authorize('delete', $ocrRequest);

        tap($ocrRequest, function ($ocrRequest) {
            $ocrRequest->orders->each->delete();
        })->delete();

        return response()->noContent();
    }
}
