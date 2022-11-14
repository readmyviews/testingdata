<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    // public $message;
    /**
     * Create a new event instance.
     *
     * @return
     */
    public function __construct($user)
    {
        $this->user = $user;
        // $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('chat');
        // return new Channel('return.chat.'.$this->user->order_item_id);
        // return new Channel('order_return_chat.'.$this->user->order_item_id);
        return new Channel('order_return_chat');
        // return new Channel('chat.'.$this->user->order_item_id);
    }
}
