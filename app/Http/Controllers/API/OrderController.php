<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
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
}
