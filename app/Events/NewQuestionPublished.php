<?php
// app/Events/QuestionResultPublished.php
class QuestionResultPublished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $result;

    public function __construct($userId, $isCorrect, $currentScore, $correctAnswer)
    {
        $this->result = [
            'user_id' => $userId,
            'is_correct' => $isCorrect,
            'user_score' => $currentScore,
            'correct_answer' => $correctAnswer,
            // Bạn có thể lấy top 10 từ Redis để gửi kèm làm bảng xếp hạng
            'leaderboard' => Redis::zrevrange('quiz_scores', 0, 9, 'WITHSCORES')
        ];
    }

    public function broadcastOn()
    {
        return new Channel('quiz-room');
    }
}