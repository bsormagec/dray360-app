<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public string $requestId;
    public array $latestStatus;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $latestStatus)
    {
        $this->latestStatus = $latestStatus;
        $this->requestId = $latestStatus['request_id'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->latestStatus['order_id']) {
            return [
                new PrivateChannel('request-status-updated-company'.$this->latestStatus['company_id'].'-orders'),
                new PrivateChannel('request-status-updated-company'.$this->latestStatus['company_id'].'-order'.$this->latestStatus['order_id']),
                new PrivateChannel('request-status-updated-orders'),
                new PrivateChannel('request-status-updated-order'.$this->latestStatus['order_id']),
            ];
        }

        return [
            new PrivateChannel('request-status-updated'),
            new PrivateChannel('request-status-updated-company'.$this->latestStatus['company_id']),
        ];
    }
}
