<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessVideoExport;
use Illuminate\Support\Str; // Dùng cho Str::slug

class VideoExportController extends Controller
{
    /**
     * Nhận request xuất video, lưu file và đẩy Job vào Queue.
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu cần thiết
        // 💡 ĐÃ SỬA: Dùng 'json' thay vì 'array' vì dữ liệu được gửi là chuỗi JSON qua FormData.
        $validated = $request->validate([
            'lyrics' => 'required|json', // Toàn bộ JSON data của project
            'audio_file' => 'required|file|mimes:mp3,wav,ogg|max:50000', // max 50MB
            'background_file' => 'nullable|file|mimes:mp4,mov,jpg,jpeg,png|max:100000', // Video hoặc Image (max 100MB)
            'global_settings' => 'required|json', // Cài đặt chung (font, size, ratio)
        ]);

        // 2. Lưu các file đã upload
        try {
            // Lưu Audio file
            $audioPath = $validated['audio_file']->store('exports/audio', 'public');

            // Lưu Background (Video/Image) file (nếu có)
            $backgroundPath = null;
            if (isset($validated['background_file'])) {
                $backgroundPath = $validated['background_file']->store('exports/backgrounds', 'public');
            }
            
            // 3. Chuẩn bị dữ liệu cho Job
            // 💡 ĐÃ THÊM: Decode chuỗi JSON thành mảng PHP
            $lyricsData = json_decode($validated['lyrics'], true);
            $globalSettings = json_decode($validated['global_settings'], true);
            
            // Tên file đầu ra (sử dụng tên audio đã làm sạch)
            $originalName = pathinfo($validated['audio_file']->getClientOriginalName(), PATHINFO_FILENAME);
            $outputFileName = 'Video_' . Str::slug($originalName) . '_' . time() . '.mp4';


            // 4. Dispatch Job (Đẩy tác vụ nặng vào hàng đợi)
            ProcessVideoExport::dispatch(
                $audioPath,
                $backgroundPath,
                $lyricsData, // Đã là mảng
                $globalSettings, // Đã là mảng
                $outputFileName,
                auth()->id() // Giả sử có authentication
            );
            
            // 5. Trả về thông báo thành công (không chờ đợi kết quả)
            return response()->json([
                'message' => 'Yêu cầu xuất video đã được nhận và đang xử lý trong nền.',
                'job_dispatched' => true,
                'file_name' => $outputFileName,
            ], 202); // HTTP 202 Accepted
            
        } catch (\Exception $e) {
            // Dọn dẹp các file đã lưu nếu có lỗi xảy ra sau khi lưu
            if (isset($audioPath)) {
                Storage::disk('public')->delete($audioPath);
            }
            if (isset($backgroundPath)) {
                Storage::disk('public')->delete($backgroundPath);
            }
            
            return response()->json([
                'message' => 'Lỗi khi xử lý yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }
}