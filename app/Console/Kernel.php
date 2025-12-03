<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Chạy lệnh mỗi phút một lần để kiểm tra suất chiếu hết hạn
        $schedule->command('showtime:update-status')->everyFiveMinutes();

        // Hoặc chạy mỗi 5 phút nếu bạn không cần độ chính xác quá cao để giảm tải
        // $schedule->command('showtime:update-status')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
