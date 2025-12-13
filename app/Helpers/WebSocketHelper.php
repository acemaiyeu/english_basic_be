<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http; // Dùng để gọi API nếu cần
// use Pusher\Pusher; // Hoặc sử dụng Pusher SDK nếu cần

class WebSocketHelper
{
    /**
     * Lấy tổng số người dùng đang kết nối đến một kênh cụ thể.
     * * @param string $channelName Tên kênh (ví dụ: 'channel-quiz-123')
     * @return int Số lượng người dùng
     */
    public static function getTotalUsersInChannel(string $channelName): int
    {
        // ----------------------------------------------------
        // LOGIC LẤY SỐ LƯỢNG NGƯỜI DÙNG THỰC TẾ SẼ NẰM Ở ĐÂY
        // ----------------------------------------------------
        
        // Ví dụ tạm thời để tránh lỗi "Class not found" khi bạn đang phát triển
        return self::getUsersFromPusherApi($channelName);
    }
    
    /**
     * Phương thức ví dụ sử dụng API của Pusher hoặc Laravel WebSockets
     * để lấy số lượng thành viên trong kênh Presence.
     * * Lưu ý: Phương thức này chỉ hoạt động với Kênh Presence (tên bắt đầu bằng 'presence-').
     * Nếu bạn dùng kênh Public, số lượng thành viên thường không được đếm.
     * * @param string $channelName
     * @return int
     */
    protected static function getUsersFromPusherApi(string $channelName): int
    {
        // Kiểm tra xem kênh có phải là Presence Channel hay không
        if (!str_starts_with($channelName, 'presence-')) {
            // Nếu là Public Channel, thường không có thông tin thành viên (trừ khi dùng các mẹo khác)
            // Bạn có thể trả về 0 hoặc một giá trị mặc định.
            return 10;
        }

        // Cấu hình Pusher (hoặc Laravel WebSockets)
        try {
            // Sử dụng Pusher SDK để truy vấn API
            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    // Nếu dùng Laravel WebSockets, có thể cần thêm wsHost và wsPort
                ]
            );

            // Truy vấn thông tin kênh. 'info' => 'user_count' để chỉ lấy số lượng
            $response = $pusher->get_channel_info($channelName, ['info' => 'user_count']);

            return $response->user_count ?? 0;

        } catch (\Exception $e) {
            // Xử lý lỗi (ví dụ: không kết nối được đến Pusher API)
            \Log::error("Lỗi khi truy vấn Pusher API: " . $e->getMessage());
            return 0;
        }
    }
}