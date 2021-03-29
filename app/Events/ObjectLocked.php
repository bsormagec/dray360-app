<?php

namespace App\Events;

use App\Models\ObjectLock;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ObjectLocked implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public array $objectLock;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ObjectLock $objectLock)
    {
        $this->objectLock = $objectLock->load('user')->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('object-locking');
    }
}
