<?php
// app/Events/QuizSessionUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizSessionUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $sessionId;
    public $data;

    public function __construct($sessionId, $data)
    {
        $this->sessionId = $sessionId;
        $this->data = $data;
    }

    // Tên kênh sẽ là `quiz.{session_id}`
    public function broadcastOn(): array
    {
        return [
            new Channel("quiz.{$this->sessionId}"),
        ];
    }

    // Tên sự kiện (event name)
    public function broadcastAs()
    {
        return 'session.update';
    }
}