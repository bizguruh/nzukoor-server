<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $user;

    /**
     * Message details
     *
     * @var Message
     */
    public $message;
    public $user_connection_id;

    public function __construct($user, $message, $user_connection_id)

    {
        $this->user = $user;
        $this->message = $message;
        $this->user_connection_id = $user_connection_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new channel('inbox.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'inboxSent';
    }
}
