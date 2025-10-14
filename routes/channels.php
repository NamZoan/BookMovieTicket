<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Thêm channel cho showtime
Broadcast::channel('showtime.{showtimeId}', function ($showtimeId) {
    return true; // Cho phép tất cả người dùng truy cập kênh này
});
