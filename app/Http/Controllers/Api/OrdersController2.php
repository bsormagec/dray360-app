<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Queries\OrdersListQuery;
use App\Http\Resources\OrdersJson;
use App\Http\Controllers\Controller;

class OrdersController2 extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = (new OrdersListQuery())->paginate(25);

        return OrdersJson::collection($orders);
    }
}
