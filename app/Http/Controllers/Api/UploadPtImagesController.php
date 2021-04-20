<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DictionaryItem;
use App\Models\OCRRequestStatus;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToUpdateStatus;
use Illuminate\Auth\Access\AuthorizationException;

class UploadPtImagesController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required',
            'original_filename' => 'required',
            'uploading_filename' => 'required',
            'datetime_utciso' => 'required',
            'company_id' => 'required|exists:t_companies,id',
            'tms_shipment_id' => 'required',
            'pt_image_type' => 'required|exists:t_dictionary_items,id',
            'order_id' => 'sometimes|nullable'
        ]);

        $this->validatePermissions($data['company_id']);

        $company = Company::find($data['company_id'], ['id', 'default_tms_provider_id']);
        $data = [
            'order_id' => $data['order_id'] ?? null,
            'status' => OCRRequestStatus::UPLOAD_IMAGE_REQUESTED,
            'request_id' => $data['request_id'],
            'company_id' => $company->id,
            'status_metadata' => [
                'tms_provider_id' => $company->default_tms_provider_id,
                'user_id' => auth()->id(),
                'company_id' => $company->id,
                'order_id' => $data['order_id'] ?? null,
                'request_id' => $data['request_id'],
                'tms_shipment_id' => $data['tms_shipment_id'],
                'pt_image_type' => DictionaryItem::find($data['pt_image_type'])->item_key,
                'datetime_utciso' => $data['datetime_utciso'],
                'original_filename' => $data['original_filename'],
                's3_bucket_name' => config('filesystems.disks.s3.bucket'),
                's3_object_key' => $data['uploading_filename'],
            ]
        ];

        $response = app(PublishSnsMessageToUpdateStatus::class)($data);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }

    protected function validatePermissions($companyId)
    {
        $hasPermissionToUpload = auth()->user()->isAbleTo('pt-images-create');

        if (
            (auth()->user()->isAbleTo('all-companies-view') && $hasPermissionToUpload)
            || ($hasPermissionToUpload && auth()->user()->getCompanyId() == $companyId)
        ) {
            return;
        }

        throw new AuthorizationException('You\'re not authorized to perform this action');
    }
}
