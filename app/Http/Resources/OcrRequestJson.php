<?php

namespace App\Http\Resources;

use App\Models\OCRRequestStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class OcrRequestJson extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ocrRequest = $this->resource->toArray();

        if ($ocrRequest['latest_ocr_request_status']) {
            $message = null;
            $metaData = $ocrRequest['latest_ocr_request_status']['status_metadata'];

            switch ($ocrRequest['latest_ocr_request_status']['status']) {
                case OCRRequestStatus::INTAKE_REJECTED:
                case OCRRequestStatus::INTAKE_EXCEPTION:
                case OCRRequestStatus::OCR_WAITING:
                case OCRRequestStatus::OCR_POST_PROCESSING_ERROR:
                case OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_ERROR:
                case OCRRequestStatus::FAILURE_SENDING_TO_WINT:
                case OCRRequestStatus::FAILURE_UPDATING_TO_WINT:
                    $message = $metaData['exception_message'] ?? null;
                    break;
                case OCRRequestStatus::SHIPMENT_NOT_CREATED_BY_WINT:
                case OCRRequestStatus::SHIPMENT_NOT_UPDATED_BY_WINT:
                    $message = $metaData['message'] ?? null;
                    break;
                case OCRRequestStatus::SUCCESS_SENDING_TO_WINT:
                case OCRRequestStatus::SUCCESS_UPDATING_TO_WINT:
                    $message = $metaData['status_message'] ?? null;
                    break;
                default:
                    $message = null;
                    break;
            }

            $ocrRequest['latest_ocr_request_status']['display_message'] = $message;
            unset($ocrRequest['latest_ocr_request_status']['status_metadata']);
        }

        return $ocrRequest;
    }
}
