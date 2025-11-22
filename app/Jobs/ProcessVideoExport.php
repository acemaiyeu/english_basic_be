<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// Thư viện cần thiết cho việc tạo video nền và chạy lệnh
use Symfony\Component\Process\Process; 

use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\CustomFilter; 
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;

class ProcessVideoExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Khai báo tất cả các thuộc tính
    protected $audioPath;
    protected $backgroundPath;
    protected $lyricsData;
    protected $globalSettings;
    protected $outputFileName; 
    protected $userId;

    public function __construct(
        string $audioPath, 
        ?string $backgroundPath, 
        array $lyricsData, 
        array $globalSettings, 
        string $outputFileName,
        int $userId = null
    ) {
        $this->audioPath = $audioPath;
        $this->backgroundPath = $backgroundPath;
        $this->lyricsData = $lyricsData;
        $this->globalSettings = $globalSettings;
        $this->outputFileName = $outputFileName;
        $this->userId = $userId;
    }

     public function handle(): void
    {
        Log::info("Bắt đầu xử lý Job xuất video: {$this->outputFileName}");

        // Lấy đường dẫn tuyệt đối của các file
        $fullAudioPath = Storage::disk('public')->path($this->audioPath);
        $fullOutputPath = Storage::disk('public')->path('exports/finished/' . $this->outputFileName);
        
        // [SỬA LỖI ĐƯỜNG DẪN OUTPUT]: 
        // 1. Chuẩn hóa đường dẫn output về Windows style (dùng \).
        $fullOutputPath = str_replace('/', '\\', $fullOutputPath);

        // 2. Đảm bảo thư mục đích tồn tại.
        $outputDirectory = dirname($fullOutputPath);
        if (!file_exists($outputDirectory)) {
            // Tạo thư mục (recursive: true)
            if (!mkdir($outputDirectory, 0755, true) && !is_dir($outputDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputDirectory));
            }
        }
        
        $width = $this->globalSettings['previewRatio']['width'] ?? 960;
        $height = $this->globalSettings['previewRatio']['height'] ?? 540;
        
        $assFilePath = null; 

        try {
            // [CẤU HÌNH FFmpeg]
            $ffmpegBinary = 'C:\\ffmpeg\\bin\\ffmpeg.exe'; 
            
            // FFmpeg dùng để lấy thông tin audio duration
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => $ffmpegBinary, 
                'ffprobe.binaries' => 'C:\\ffmpeg\\bin\\ffprobe.exe',
                'timeout'          => 3600, 
                'ffmpeg.threads'   => 4,
            ]);

            // 1. Tạo file ASS 
            $assFilePath = sys_get_temp_dir() . '/' . uniqid('lyrics_') . '.ass';
            $this->generateAssFile($assFilePath); 

            // [GIẢI PHÁP CUỐI CÙNG]: Unix Path + Escape Colon + Escape Backslash
            // 1. Chuẩn hóa đường dẫn: Dùng forward slash (/)
            $safeAssPath = str_replace('\\', '/', $assFilePath); 
            
            // 2. Escape dấu hai chấm (:) bằng dấu gạch chéo ngược (\:) (FFmpeg syntax)
            $safeAssPath = str_replace(':', '\:', $safeAssPath); 
            
            // 3. THÊM: Escape dấu gạch chéo ngược (\) thành hai lần (\\) (Shell/PHP Process syntax)
            $safeAssPath = str_replace('\\', '\\\\', $safeAssPath);
            

            // 2. Xây dựng Lệnh Shell FFmpeg Complex Filter
            $inputVideoArguments = [];
            $inputAudioArgument = escapeshellarg($fullAudioPath); 
            
            // Dùng đường dẫn đã escape và chuẩn hóa
            // SỬA ĐỔI QUAN TRỌNG: BỎ TIỀN TỐ "file="
            $complexFilter = "subtitles=" . $safeAssPath; // Đã bỏ 'file='

            if ($this->backgroundPath) {
                // Video Input là Background đã upload
                $inputVideoPath = Storage::disk('public')->path($this->backgroundPath);
                $inputVideoArguments = ['-i', escapeshellarg($inputVideoPath)];
                
            } else {
                // Không có Background: Sử dụng Filter Complex để tạo nền đen
                $audioMedia = $ffmpeg->open($fullAudioPath);
                $audioDuration = $audioMedia->getFormat()->get('duration');
                
                Log::warning("Không có Background. Đang dùng Complex Filter để tạo video nền đen dài " . round($audioDuration, 2) . "s.");
                
                // Input giả lập nền đen (lavfi)
                $inputVideoArguments = [
                    '-f', 'lavfi', 
                    '-i', 'color=c=black:s='.$width.'x'.$height.':r=25:d='.($audioDuration + 1), // Thêm duration
                ];
            }

            // 3. Xây dựng Lệnh Shell FFmpeg Cuối cùng
            $commandArray = array_merge(
                [$ffmpegBinary, '-y'], // Binary và cờ ghi đè
                $inputVideoArguments,
                ['-i', $inputAudioArgument], // Input Audio
                [
                    // Áp dụng Complex Filter (subtitles)
                    '-vf', $complexFilter,
                    
                    // Cấu hình Output Video/Audio
                    '-c:v', 'libx264',
                    '-pix_fmt', 'yuv420p',
                    '-c:a', 'aac',
                    '-b:a', '128k',
                    '-shortest', 
                    
                    escapeshellarg($fullOutputPath) // File output cuối cùng
                ]
            );

            // 4. Thực thi Lệnh Shell
            // Chạy lệnh thông qua shell
            $process = Process::fromShellCommandline(implode(' ', $commandArray));
            $process->setTimeout(3600);
            $process->run();

            if (!$process->isSuccessful()) {
                // Lỗi Encoding xảy ra ở đây. Ném lỗi chi tiết hơn.
                throw new \RuntimeException(
                    "FFmpeg encoding failed. Error Output: " . $process->getErrorOutput() . "\n Full Command: " . $process->getCommandLine()
                );
            }
            
            Log::info("Hoàn thành xuất video: {$this->outputFileName}");
            
            // 5. Dọn dẹp
            // Xóa file audio, background (nếu có) và file ASS tạm thời
            Storage::disk('public')->delete($this->audioPath);
            if ($this->backgroundPath) {
                 Storage::disk('public')->delete($this->backgroundPath);
            }
            if (file_exists($assFilePath)) {
                 unlink($assFilePath);
            }

        } catch (\Exception $e) {
            Log::error("Lỗi Job xuất video: {$this->outputFileName} - " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->fail($e);
        }
    }

    /**
     * Tạo file ASS từ dữ liệu lyric.
     */
    // protected function generateAssFile(string $assFilePath): void
    // {
    //     // ... (Hàm generateAssFile giữ nguyên) ...
    //     $assContent = "[Script Info]\nScriptType: v4.00+\n[V4+ Styles]\nStyle: Default,Arial,20,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,0,1,1,2,5,10,10,10,0\n[Events]\n";
        
    //     foreach ($this->lyricsData as $lyric) {
    //         // Chuyển đổi giây sang định dạng HH:MM:SS.cs (centiseconds)
    //         $startTime = gmdate("H:i:s", floor($lyric['time'])) . "." . str_pad(round(($lyric['time'] - floor($lyric['time'])) * 100), 2, '0', STR_PAD_LEFT);
    //         $endTime = gmdate("H:i:s", floor($lyric['time'] + $lyric['duration'])) . "." . str_pad(round((($lyric['time'] + $lyric['duration']) - floor($lyric['time'] + $lyric['duration'])) * 100), 2, '0', STR_PAD_LEFT);
            
    //         // Dòng ASS cơ bản:
    //         $assContent .= "Dialogue: 0,{$startTime},{$endTime},Default,,0,0,0,," . $lyric['text'] . "\n";
    //     }
        
    //     file_put_contents($assFilePath, $assContent);
    // }
    protected function generateAssFile(string $assFilePath): void
    {
        // ... (Header giữ nguyên)
        // [V4+ Styles] - Thêm Style cho Karaoke (Highlight Color)
        // Secondary color (&H000000FF) là màu mặc định (chưa hát)
        // Primary color (&H00FFFFFF) là màu đã hát (highlight color)
        $assContent = "[Script Info]\nScriptType: v4.00+\n[V4+ Styles]\nStyle: Default,Arial,30,&H00FFFFFF,&H000000FF,&H00000000,&H00000000,0,0,0,0,100,100,0,0,1,1,2,5,10,10,10,0\n[Events]\n";
        
        foreach ($this->lyricsData as $lyric) {
            // Chuẩn bị thời gian bắt đầu và kết thúc cả câu
            $startTime = gmdate("H:i:s", floor($lyric['time'])) . "." . str_pad(round(($lyric['time'] - floor($lyric['time'])) * 100), 2, '0', STR_PAD_LEFT);
            $endTime = gmdate("H:i:s", floor($lyric['time'] + $lyric['duration'])) . "." . str_pad(round((($lyric['time'] + $lyric['duration']) - floor($lyric['time'] + $lyric['duration'])) * 100), 2, '0', STR_PAD_LEFT);
            
            $textWithEffect = "";
            
            // Nếu có dữ liệu words (giả định)
            if (isset($lyric['words']) && is_array($lyric['words'])) {
                foreach ($lyric['words'] as $word) {
                    // Chuyển đổi thời lượng (seconds) sang centiseconds (cs) cho thẻ \k
                    // time * 100 = thời gian tính bằng centiseconds
                    $k_time = round($word['duration'] * 100); 
                    
                    // Thẻ {\kXX} áp dụng hiệu ứng highlight trong XX centiseconds
                    $textWithEffect .= "{\k" . $k_time . "}" . $word['text'];
                }
            } else {
                // Nếu không có word timing, chỉ hiển thị cả câu (không hiệu ứng)
                $textWithEffect = $lyric['text'];
            }
            
            // Dòng ASS cơ bản:
            // "Dialogue: 0,<Start>,<End>,Default,,0,0,0,,<Text>"
            $assContent .= "Dialogue: 0,{$startTime},{$endTime},Default,,0,0,0,," . $textWithEffect . "\n";
        }
        
        file_put_contents($assFilePath, $assContent);
    }
}