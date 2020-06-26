<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Queries\OcrRequestOrderListQuery;
use App\Http\Resources\Orders as OrdersResource;

class OrdersController extends Controller
{
    // expire JPG download URI after this many seconds
    const MINUTES_URI_REMAINS_VALID = 15;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $ocrRequestsOrders = (new OcrRequestOrderListQuery())->paginate(25);

        return OrdersResource::collection($ocrRequestsOrders);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order = $order->load([
                'ocrRequest',
                'ocrRequest.statusList',
                'ocrRequest.latestOcrRequestStatus',
                'orderLineItems',
                'billToAddress',
                'portRampOfDestinationAddress',
                'portRampOfOriginAddress',
                'orderAddressEvents',
                'orderAddressEvents.address',
            ]);

        // add a presigned download URI to each
        try {
            $ocr_clone = $order->ocr_data;
            // note the & in the foreach specifies pass-by-reference
            foreach ($ocr_clone['page_index_filenames']['value'] as $eachPageIndex => &$eachPage) {
                $splitString = preg_split('|\/|', $eachPage['value']);
                $s3Config = config('filesystems.disks.s3-base') + ['bucket' => $splitString[2]];
                $storage = Storage::createS3Driver($s3Config);
                $urlExpiryTime = now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);

                // save presigned info on eachPage
                $eachPage['presigned_download_uri'] = $storage->temporaryUrl($splitString[3], $urlExpiryTime);
                $eachPage['presigned_download_uri_expires'] = $urlExpiryTime;
            }
            // assign updated ocr_data clone to order object, replacing old ocr_data
            $order->ocr_data = $ocr_clone;
        } catch (\Exception $e) {
            // todo: write something to the laravel error log
        }

        // all done
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $orderData = $request->validate(Order::$rules);
        $relatedModels = $request->validate([
            'order_line_items' => ['sometimes', 'array'],
            'order_line_items.*.t_order_id' => ['required', "in:{$order->id}"],
            'order_line_items.*.id' => 'present',
            'order_line_items.*.deleted_at' => 'sometimes',
            'order_address_events' => ['sometimes', 'array'],
            'order_address_events.*.id' => 'present',
            'order_address_events.*.t_order_id' => ['required', "in:{$order->id}"],
            'order_address_events.*.t_address_id' => ['nullable', 'exists:t_addresses,id'],
        ]);

        $order->update($orderData);
        $order->updateRelatedModels($relatedModels);

        return response()->json($order);
    }
}
