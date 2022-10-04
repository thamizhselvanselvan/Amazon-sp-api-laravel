<?php

namespace App\Events;

use App\Models\Catalog\Catalog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class checkEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $catalog;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($catalog)
    {
       $this->catalog = $catalog ;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('testing-channel');
    }

    // public function broadcastAs()
    // {
    //     return 'test-event';
    // }
}
