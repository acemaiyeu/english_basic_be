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
    public $channelName; // 💡 THÊM: Thuộc tính để lưu tên kênh động

    public function __construct($message, $index_question, $channelName) // 💡 THÊM: Nhận tên kênh
    {
        // Gán giá trị vào thuộc tính public
        $this->message = $message; 
        $this->index_question = $index_question;
        $this->channelName = $channelName; // Gán tên kênh
    }

    public function broadcastOn()
    {
        // 💡 SỬA LỖI: Sử dụng tên kênh động (ví dụ: 'default-gamequiz-channel')
        return new Channel($this->channelName); 
    }
    
    public function broadcastAs()
    {
        // Tên sự kiện (phải khớp với .listen('.quiz.message.sent', ...) trong React)
        return 'quiz.message.sent'; 
    }

    // 💡 ĐÃ SỬA LỖI LOGIC TĂNG INDEX
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            // 💡 CHỈ SỬ DỤNG GIÁ TRỊ NHẬN ĐƯỢC (đã là chỉ số kế tiếp)
            'index_question' => $this->index_question 
        ];
    }
}