<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $table = 'showtimes';

    // Khóa chính của bảng
    protected $primaryKey = 'showtime_id';

    // Các trường có thể được gán đại trà (mass assignable)
    protected $fillable = [
        'movie_id',
        'screen_id',
        'show_date',
        'show_time',
        'end_time',
        'price_seat_normal',
        'price_seat_vip',
        'price_seat_couple',
        'available_seats',
        'status'
    ];

    protected $casts = [
        'price_seat_normal' => 'decimal:2',
        'price_seat_vip' => 'decimal:2',
        'price_seat_couple' => 'decimal:2',
        'show_date' => 'date',
        'show_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    // Quan hệ với bảng Movie (Mỗi suất chiếu thuộc về một bộ phim)
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    // Quan hệ với bảng Screen (Mỗi suất chiếu thuộc về một phòng chiếu)
    public function screen()
    {
        return $this->belongsTo(Screen::class, 'screen_id');
    }

    // Helper method để lấy giá theo loại ghế
    public function getPriceByType($seatType)
    {
        return match($seatType) {
            'VIP' => $this->price_seat_vip,
            'Couple' => $this->price_seat_couple,
            default => $this->price_seat_normal,
        };
    }
}
