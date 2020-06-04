<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    // expire JPG download URI after this many seconds
    const MINUTES_URI_REMAINS_VALID = 15;

    /**
     * Get list of orders
     *
     * @param  [Request] $request
     * @return [json] list of orders
     */
    public function orders(Request $request)
    {
        $orders = Order::with([
                'ocrRequest',
                'ocrRequest.statusList',
                'ocrRequest.latestOcrRequestStatus',
                'orderLineItems',
                'billToAddress',
                'portRampOfDestinationAddress',
                'portRampOfOriginAddress',
                'orderAddressEvents',
                'orderAddressEvents.address',
            ])
            ->paginate(25);

        return \App\Http\Resources\Orders::collection($orders);
    }

    /**
     * Get a single order, with all detail. Especially, return a
     * presigned GET request for downloading the JPG from S3
     *
     * @param  [Request] $request
     * @param  int $orderId
     * @return \App\Models\Order list of orders
     */
    public function order($orderId)
    {
        $order = Order::with([
                'ocrRequest',
                'ocrRequest.statusList',
                'ocrRequest.latestOcrRequestStatus',
                'orderLineItems',
                'billToAddress',
                'portRampOfDestinationAddress',
                'portRampOfOriginAddress',
                'orderAddressEvents',
                'orderAddressEvents.address',
            ])
            ->findOrFail($orderId);

        // Create an S3 client
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'us-east-2')
        ]);

        // add a presigned download URI to each
        try {
            $ocr_clone = $order->ocr_data;
            // note the & in the foreach specifies pass-by-reference
            foreach ($ocr_clone['page_index_filenames']['value'] as $eachPageIndex => &$eachPage) {
                $jpgURI = $eachPage['value'];
                $jpgURISplit = preg_split('|\/|', $jpgURI);
                $bucket = $jpgURISplit[2];
                $key = $jpgURISplit[3];
                $s3Command = $s3Client->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key' => $key
                ]);
                $urlExpiryTime = Carbon::now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);
                $presignedRequest = $s3Client->createPresignedRequest($s3Command, $urlExpiryTime);
                $downloadUri = (string) $presignedRequest->getUri()->__toString();

                // save presigned info on eachPage
                $eachPage['presigned_download_uri'] = $downloadUri;
                $eachPage['presigned_download_uri_expires'] = $urlExpiryTime;
            }
            // assign updated ocr_data clone to order object, replacing old ocr_data
            $order->ocr_data = $ocr_clone;
        } catch (\Exception $e) {
            // do nothing, just silently fail if we can't process any of this.
            // todo: write something to the laravel error log
        }

        // all done
        return response()->json($order);
    }
}
