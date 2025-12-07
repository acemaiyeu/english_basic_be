<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('default-gamequiz-channel'); 
    }

    public function broadcastAs()
    {
        return 'quiz.message.sent'; 
    }

    // Quan trọng: Chỉ gửi nội dung message mà frontend cần
    public function broadcastWith()
    {
        return [
            'message' => $this->message, 
            'index_question' => $this->index_question + 1
            // Frontend sẽ truy cập data.message
            // Có thể thêm 'user_id', 'timestamp', v.v.
        ];
    }
    // ...
}
