<?php 
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;
    
    public $message;
    public $index_question;
    public $channelName;
    public $user; // 💡 THÊM: Thuộc tính để lưu thông tin User

    public function __construct($message, $index_question, $channelName, $user) // 💡 CẬP NHẬT: Nhận thông tin User
    {
        // Gán giá trị vào thuộc tính public
        $this->message = $message; 
        $this->index_question = $index_question;
        $this->channelName = $channelName;
        $this->user = $user; // Gán thông tin User
    }

    public function broadcastOn()
    {
        return new Channel($this->channelName); 
    }
    
    public function broadcastAs()
    {
        return 'quiz.message.sent'; 
    }

    // 💡 CẬP NHẬT: Thêm 'user' vào dữ liệu phát sóng
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'index_question' => $this->index_question,
            'user' => $this->user, // 💡 THÊM: Thông tin User,
            'total_users' => \App\Helpers\WebSocketHelper::getTotalUsersInChannel($this->channelName) // 💡 THÊM: Tổng số người dùng trong kênh
        ];
    }
}