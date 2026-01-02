<?php 

namespace App\Http\Controllers;

class submitAnswerController extends Controller
{
    public function submitAnswer(Request $request)
    {
        $userId = Auth::id();
        $questionId = $request->input('question_id');
        $submittedAnswer = $request->input('answer');

        // Lấy câu hỏi từ cơ sở dữ liệu
        $question = Question::find($questionId);
        if (!$question) {
            return response()->json(['error' => 'Câu hỏi không tồn tại'], 404);
        }

        // Kiểm tra đáp án
        $isCorrect = ($submittedAnswer === $question->correct_answer);

        // Cập nhật điểm số người dùng trong Redis
        $currentScore = Redis::zscore('quiz_scores', $userId) ?? 0;
        if ($isCorrect) {
            $currentScore += 10; // Tăng 10 điểm nếu đúng
            Redis::zadd('quiz_scores', $currentScore, $userId);
        }

        // Phát sự kiện kết quả câu hỏi
        event(new QuestionResultPublished($userId, $isCorrect, $currentScore, $question->correct_answer));

        return response()->json([
            'is_correct' => $isCorrect,
            'current_score' => $currentScore
        ]);
    }
}