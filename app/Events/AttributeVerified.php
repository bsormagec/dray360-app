<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class AttributeVerified
{
    use Dispatchable;
    use SerializesModels;

    public $verifiableColumn;
    public $orderData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order, $verifiableColumn)
    {
        $this->orderData = $order->toArray();
        $this->verifiableColumn = $verifiableColumn;
    }
}
