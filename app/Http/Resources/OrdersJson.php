<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use App\Models\OCRRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersJson extends ResourceCollection
{
    protected Collection $companies;

    public function __construct($resource, Collection $companies)
    {
        parent::__construct($resource);

        $this->companies = $companies;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            $order = $item->toArray();
            $order['latest_ocr_request_status'] = Arr::get($order, 'ocr_request.latest_ocr_request_status', null);
            $order['tms_template_name'] = $this->getTemplateName($item);

            if ($order['latest_ocr_request_status']) {
                $message = null;
                $metaData = $order['latest_ocr_request_status']['status_metadata'];

                switch ($order['latest_ocr_request_status']['status']) {
                    case OCRRequestStatus::INTAKE_REJECTED:
                    case OCRRequestStatus::INTAKE_EXCEPTION:
                    case OCRRequestStatus::OCR_WAITING:
                    case OCRRequestStatus::OCR_POST_PROCESSING_ERROR:
                    case OCRRequestStatus::PROCESS_OCR_OUTPUT_FILE_ERROR:
                    case OCRRequestStatus::FAILURE_SENDING_TO_WINT:
                    case OCRRequestStatus::FAILURE_UPDATING_TO_WINT:
                        $message = $metaData['exception_message'] ?? null;
                        break;
                    case OCRRequestStatus::OCR_TIMEDOUT:
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

                $order['latest_ocr_request_status']['display_message'] = $message;
                unset($order['latest_ocr_request_status']['status_metadata']);
            }

            unset($order['ocr_request']);


            return $order;
        })
        ->toArray();
    }

    protected function getTemplateName($order)
    {
        $company = $this->companies->where('id', $order->t_company_id)->first();

        if (! $company) {
            return null;
        }
        $templateList = collect(json_decode($company->profit_tools_template_list, true) ?? []);
        $template = $templateList->where('tms_template_id', $order->tms_template_id)->first();

        return $template['tms_template_name'] ?? null;
    }
}
