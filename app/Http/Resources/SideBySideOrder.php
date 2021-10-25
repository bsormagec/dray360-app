<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Order;
use App\Models\FieldMap;
use App\Models\OCRRequest;
use App\Models\OCRVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Actions\PresignImageUrl;
use App\Models\OCRRequestStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class SideBySideOrder extends JsonResource
{
    const MINUTES_URI_REMAINS_VALID = 15;
    protected bool $preSignImages;

    public function __construct(Order $order, bool $preSignImages = true)
    {
        parent::__construct(
            $order->loadRelationshipsForSideBySide()->load('precededByOrder')
        );
        self::withoutWrapping();
        $this->preSignImages = $preSignImages;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->preSignImages) {
            $this->preSignDocumentImages();
            $this->loadAndSignPtImages();
        }

        $this->preparePreceedingOrderChanges();
        $this->loadOrderCompanyConfiguration();
        $this->loadFieldMappingConfiguration();
        $this->loadLatestOcrRequestLatestStatus();
        $this->loadUserWhoSentToTms();
        $this->loadLocks();
        $this->loadSiblingOrdersPagination();

        return parent::toArray($request);
    }

    protected function loadLatestOcrRequestLatestStatus()
    {
        $request = OCRRequest::whereNull('order_id')
            ->where('request_id', $this->resource->request_id)
            ->with('latestOcrRequestStatus:id,request_id,status')
            ->first();

        $this->resource->setRelation('parentOcrRequest', $request);
    }

    protected function loadOrderCompanyConfiguration()
    {
        $this->resource->load(['company:id,configuration,name,t_domain_id,default_tms_provider_id']);

        if (! $this->resource->company) {
            return;
        }

        $configuration = app('tenancy')->getCompanyConfiguration($this->resource->company);
        $this->resource->company->unsetRelation('domain');

        $this->resource->company->configuration = $configuration;
    }

    protected function loadFieldMappingConfiguration()
    {
        $variant = OCRVariant::where('abbyy_variant_name', $this->resource->variant_name)->first(['id']);

        $fieldMap = FieldMap::getFrom([
            'company_id' => $this->resource->t_company_id,
            'variant_id' => $variant->id ?? null,
        ]);

        $this->resource->setRelation('fieldMaps', collect($fieldMap));
    }

    protected function loadLocks()
    {
        $this->resource->load([
            'locks.user' => function ($load) {
                $load->select('id', 'name', 't_company_id');
            },
        ]);

        $lock = $this->resource->getActiveLock();
        $isLocked = $this->resource->isLockedForTheUser();

        $this->resource->unsetRelation('locks');
        $this->resource->is_locked = ! $lock || $isLocked;
        $this->resource->ocr_request_is_locked = $isLocked;
        $this->resource->setRelation('lock', $lock);
    }

    protected function loadUserWhoSentToTms()
    {
        $status = OCRRequestStatus::query()
            ->select(['status_metadata', 'created_at'])
            ->where([
                'request_id' => $this->resource->request_id,
                'order_id' => $this->resource->id,
            ])
            ->where('status', 'like', 'sending-to-%')
            ->first();

        if (! $status) {
            $this->resource->ocrRequest->setRelation('sentToTms', null);
            return;
        }

        $userId = $status->status_metadata['user_id'] ?? null;

        if (! $userId) {
            $this->resource->ocrRequest->setRelation('sentToTms', null);
            return;
        }

        $status->setRelation('user', User::find($userId, ['name', 'email']));
        $this->resource->ocrRequest->setRelation('sentToTms', $status);
    }

    protected function loadAndSignPtImages()
    {
        try {
            $ocrData = $this->resource->ocr_data;
            $pageCount = count($ocrData['page_index_filenames']['value']);

            OCRRequestStatus::query()
            ->where([
                'status' => OCRRequestStatus::UPLOAD_IMAGE_SUCCEEDED,
                'order_id' => $this->resource->id,
            ])
            ->get()
            ->map(function ($request, $index) use ($pageCount) {
                return [
                    'name' => 'page_index_'. ($pageCount + $index + 1),
                    'value' => $request->status_metadata['event_info']['original_filename'],
                    'presigned_download_uri' => (new PresignImageUrl())(
                        $request->status_metadata['event_info']['s3_bucket_name'],
                        $request->status_metadata['event_info']['s3_object_key']
                    ),
                    'presigned_download_uri_expires' => PresignImageUrl::MINUTES_URI_REMAINS_VALID
                ];
            })->each(function ($image, $index) use (&$ocrData, $pageCount) {
                $newIndex = $pageCount + $index + 1;
                $ocrData['page_index_filenames']['value']["{$newIndex}"] = $image;
            });

            $this->resource->ocr_data = $ocrData;
        } catch (\Exception $e) {
        }
    }

    protected function preSignDocumentImages()
    {
        try {
            $ocr_clone = $this->resource->ocr_data;
            // note the & in the foreach specifies pass-by-reference
            foreach ($ocr_clone['page_index_filenames']['value'] as $eachPageIndex => &$eachPage) {
                $pageUri = $eachPage['value'];
                if (! is_null($pageUri)) {
                    $eachPage['presigned_download_uri'] = (new PresignImageUrl())(
                        s3_bucket_from_url($eachPage['value']),
                        s3_file_name_from_url($eachPage['value'])
                    );
                    $eachPage['presigned_download_uri_expires'] = PresignImageUrl::MINUTES_URI_REMAINS_VALID;
                }
            }
            // assign updated ocr_data clone to order object, replacing old ocr_data
            $this->resource->ocr_data = $ocr_clone;
        } catch (\Exception $e) {
        }
    }

    protected function preparePreceedingOrderChanges()
    {
        if (! $this->resource->precededByOrder) {
            $this->resource
                ->setRelation('precedingOrderChanges', collect())
                ->unsetRelation('precededByOrder');
            return;
        }

        $precededByOrder = $this->resource
            ->precededByOrder
            ->load([
                'orderLineItems',
                'billToAddress',
                'portRampOfDestinationAddress',
                'portRampOfOriginAddress',
                'orderAddressEvents.address',
                'equipmentType',
            ])
            ->toArray();

        $changedValues = collect($precededByOrder)
            ->reject(function ($precedingValue, $key) {
                if ($this->keyShouldBeIgnored($key)) {
                    return true;
                }

                if (in_array($key, ['order_line_items', 'order_address_events'])) {
                    $columnsMap = [
                        'order_address_events' => ['t_address_id'],
                        'order_line_items' => ['multiline_contents', 'contents', 'quantity', 'weight', 'weight_uom'],
                    ];


                    foreach ($columnsMap[$key] as $columnToCompare) {
                        $areTheSame = $this->relatedItemsAreTheSame(
                            $this->resource->getRelationValue(Str::camel($key))->toArray(),
                            $precedingValue,
                            $columnToCompare
                        );
                        if (! $areTheSame) {
                            return false;
                        }
                    }
                    return true;
                }

                return $this->resource->getAttribute($key) == $precedingValue;
            })
            ->mapWithKeys(function ($value, $key) use ($precededByOrder) {
                $baseValue = [$key => $value];

                if (in_array($key, [
                    'port_ramp_of_origin_address_id',
                    'port_ramp_of_destination_address_id',
                    'bill_to_address_id'
                ])) {
                    $relationObjectKey = Str::before($key, '_id');
                    return $baseValue + [$relationObjectKey => $precededByOrder[$relationObjectKey] ?? null];
                }

                return $baseValue;
            });

        $this->resource
            ->setRelation('precedingOrderChanges', $changedValues)
            ->unsetRelation('precededByOrder');
    }

    protected function loadSiblingOrdersPagination()
    {
        $siblings = Order::query()
            ->where('request_id', $this->resource->request_id)
            ->orderByDesc('id')
            ->whereHas('ocrRequest')
            ->pluck('id');
        $current = $siblings->search($this->resource->id);

        $this->resource->siblings = [
            'previous' => $siblings->get($current - 1),
            'next' => $siblings->get($current + 1),
            'current' => $current + 1,
            'total' => $siblings->count(),
        ];
    }

    protected function relatedItemsAreTheSame(array $current, array $preceding, string $columnToCompare): bool
    {
        if (count($current) != count($preceding)) {
            return false;
        }

        return collect($current)
            ->filter(function ($item, $index) use ($preceding, $columnToCompare) {
                return $item[$columnToCompare] == Arr::get($preceding, "{$index}.{$columnToCompare}");
            })->count() == count($current);
    }

    protected function keyShouldBeIgnored(string $key): bool
    {
        return in_array($key, [
            'id',
            'request_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'ocr_data',
            'bill_to_address',
            'tms_template',
            'port_ramp_of_destination_address',
            'port_ramp_of_origin_address',
            'equipment_type',
            'preceded_by_order_id',
        ]) || Str::contains($key, ['_raw_text', '_verified']);
    }
}
