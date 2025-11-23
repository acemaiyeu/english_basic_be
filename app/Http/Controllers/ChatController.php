<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Lấy câu hỏi từ người dùng
        $userMessage = $request->input('message');
        
        // 2. Cấu hình gửi đến Ollama (Localhost)
        // Lưu ý: stream = false để lấy toàn bộ câu trả lời một lần (dễ xử lý hơn streaming)
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            ])->timeout(120)->post('http://127.0.0.1:11434/api/chat', [
                'model' => 'phi3', // <--- Kiểm tra kỹ dòng này
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage]
                ],
                'stream' => false,
            ]);

        // 3. Xử lý phản hồi
        if ($response->successful()) {
            $data = $response->json();
            $aiReply = $data['message']['content'];

            // (Tùy chọn) Lưu vào Database tại đây...

            return response()->json([
                'status' => 'success',
                'reply' => $aiReply
            ]);
        }

        return response()->json(['error' => 'AI server error'], 500);
    }
}