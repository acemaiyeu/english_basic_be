<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileDownloadController extends Controller
{
    /**
     * Kiểm tra trạng thái file video đã được xử lý xong hay chưa (dùng cho Polling).
     * Endpoint: GET /api/export-status/{filename}
     */
    public function checkStatus($filename)
    {
        // Đường dẫn nơi Job ProcessVideoExport sẽ lưu file hoàn chỉnh
        // Vui lòng đảm bảo thư mục này đã tồn tại và có thể ghi: storage/app/public/exports/finished
        $path = 'exports/finished/' . $filename; 

        // Kiểm tra xem file có tồn tại trong disk 'public' hay không
        $fileExists = Storage::disk('public')->exists($path);

        if ($fileExists) {
            // File đã hoàn thành
            return response()->json([
                'is_ready' => true,
                'message' => 'Video đã được xử lý xong và sẵn sàng để tải xuống.',
            ], 200);
        }

        // File chưa hoàn thành hoặc đang xử lý
        return response()->json([
            'is_ready' => false,
            'message' => 'Video đang được xử lý trong nền, vui lòng chờ đợi.',
        ], 200);
    }

    /**
     * Xử lý download file video đã hoàn thành.
     * Endpoint: GET /api/download-video/{filename}
     */
     public function download($filename)
    {
        // 1. Xác định đường dẫn file trong thư mục Storage (internal path)
        $filePath = 'exports/finished/' . $filename;
        
        // --- BƯỚC 1: Chuẩn hóa đường dẫn trước khi kiểm tra ---
        // Đảm bảo PHP/Storage dùng dấu gạch chéo ngược (\) để tìm file trên Windows.
        $normalizedPath = str_replace('/', '\\', $filePath);

        // 2. Kiểm tra xem file có tồn tại không
        if (!Storage::disk('public')->exists($normalizedPath)) {
            // Log lỗi chi tiết để debug
            \Log::error("Download failed: File not found or path incorrect.", ['normalized_path' => $normalizedPath]);
            abort(404, 'File not found in storage. (Error Code: 404-D1)');
        }

        // 3. Lấy đường dẫn tuyệt đối của file
        // Sử dụng đường dẫn đã chuẩn hóa để lấy full path
        $fullPath = Storage::disk('public')->path($normalizedPath);
        
        // 4. Trả về file dưới dạng download
        return response()->download($fullPath, $filename, [
            'Content-Type' => 'video/mp4',
        ]);
    }
}