<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class AddressVerified
{
    use Dispatchable;
    use SerializesModels;

    public $type;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orderOrAddressEvent)
    {
        $this->type = get_class($orderOrAddressEvent);
        $this->data = $orderOrAddressEvent->toArray();
    }
}
