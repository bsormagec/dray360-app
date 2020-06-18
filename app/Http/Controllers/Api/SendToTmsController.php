<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Actions\PublishSnsMessageToSendToTms;

class SendToTmsController extends Controller
{
    const VALID_STATUSES = ['sending-to-wint'];

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::VALID_STATUSES)],
            'order_id' => 'required|exists:t_orders,id',
        ]);
        $order = Order::findOrFail($data['order_id'], ['request_id']);
        $data['request_id'] = $order->request_id;

        $response = app(PublishSnsMessageToSendToTms::class)($data);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }
}
