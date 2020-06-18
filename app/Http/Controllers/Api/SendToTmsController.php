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
            'company_id' => 'required|exists:t_companies,id',
            'tms_provider_id' => 'required|exists:t_tms_providers,id',
        ]);
        $order = Order::find($data['order_id'], ['request_id']);
        $data['request_id'] = $order->request_id;

        $response = app(PublishSnsMessageToSendToTms::class)($data);

        if ($response['status'] === 'error') {
            return response()->json(['data' => $response['message']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $response['message']]);
    }
}
