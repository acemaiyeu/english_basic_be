<?php 
// app/Http/Controllers/QuizController.php
use App\Events\QuizSessionUpdated;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    // Tạo phòng chơi và trả về Game Code
    public function createSession(Request $request, $quizId)
    {
        $sessionId = Str::random(6); // Tạo code 6 ký tự
        // Lưu Game Session vào database (ID, quiz_id, status='LOBBY')

        // Trả về mã phòng cho Frontend (host)
        return response()->json(['sessionId' => $sessionId]);
    }

    // Bắt đầu game và phát sự kiện câu hỏi đầu tiên
    public function startGame(Request $request, $sessionId)
    {
        // 1. Cập nhật status của session thành 'IN_PROGRESS'
        // 2. Lấy câu hỏi đầu tiên
        $firstQuestion ="Ai la ba Manh Hai";/* ... logic lấy câu hỏi ... */;

        // 3. Phát sự kiện real-time qua Pusher
        event(new QuizSessionUpdated($sessionId, [
            'type' => 'NEW_QUESTION',
            'question' => $firstQuestion
        ]));

        return response()->json(['message' => 'Game started']);
    }

    // Xử lý câu trả lời từ người chơi
    public function submitAnswer(Request $request, $sessionId)
    {
        // 1. Xử lý logic tính điểm, kiểm tra đáp án
        // 2. Cập nhật điểm người chơi trong database
        // 3. (Tuỳ chọn) Cập nhật bảng xếp hạng real-time
        $leaderboard =  10;/* ... logic tính toán ... */;

        event(new QuizSessionUpdated($sessionId, [
            'type' => 'LEADERBOARD_UPDATE',
            'leaderboard' => $leaderboard
        ]));

        return response()->json(['message' => 'Answer submitted']);
    }
}