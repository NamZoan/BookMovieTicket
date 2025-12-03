<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Showtime; // Đảm bảo bạn đã có model Showtime

class UpdateShowtimeStatus extends Command
{
    /**
     * Tên lệnh để chạy trong terminal.
     *
     * @var string
     */
    protected $signature = 'showtime:update-status';

    /**
     * Mô tả lệnh.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái các suất chiếu đã qua thành over';

    /**
     * Thực thi console command.
     */
    public function handle()
    {
        // Lấy thời gian hiện tại
        $now = Carbon::now();
        $currentDate = $now->toDateString(); // Y-m-d
        $currentTime = $now->toTimeString(); // H:i:s

        $this->info("Đang kiểm tra các suất chiếu hết hạn lúc: $now");

        // Logic cập nhật:
        // Cập nhật status = 'over' CHO NHỮNG DÒNG:
        // 1. Status hiện tại KHÔNG PHẢI là 'over'
        // 2. VÀ (Ngày chiếu nhỏ hơn ngày hiện tại HOẶC (Ngày chiếu bằng ngày hiện tại VÀ Giờ chiếu nhỏ hơn giờ hiện tại))
        
        $updatedCount = Showtime::where('status', '!=', 'Over')
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($currentDate, $currentTime) {
                $query->where('show_date', '<', $currentDate)
                      ->orWhere(function ($q) use ($currentDate, $currentTime) {
                          $q->where('show_date', '=', $currentDate)
                            ->where('show_time', '<', $currentTime);
                      });
            })
            ->update(['status' => 'Over']);

        $this->info("Đã cập nhật $updatedCount suất chiếu sang trạng thái 'over'.");
    }
}