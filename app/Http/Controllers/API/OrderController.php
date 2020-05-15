<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{

    /**
     * Get list of orders
     *
     * @param  [Request] $request
     * @return [json] list of orders
     */
    public function orders(Request $request)
    {
        $orders = Order::paginate(25);
        return \App\Http\Resources\Orders::collection($orders);
    }

    public function order(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)->get()->first();
        return response()->json($order);
    }
}
