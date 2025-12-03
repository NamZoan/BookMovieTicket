<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
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

    /**
     * Get the start datetime for this showtime in a robust way.
     * Handles cases where show_time may inadvertently contain a full datetime
     * or when show_date may include a time component.
     *
     * @return \Carbon\Carbon
     */
    public function getStartDateTime(): \Carbon\Carbon
    {
        // Use the original DB values to make a reliable decision
        $rawDate = (string) $this->getOriginal('show_date');
        $rawTime = (string) $this->getOriginal('show_time');

        // If show_time already contains a full date (YYYY-MM-DD), prefer it
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $rawTime)) {
            return Carbon::parse($rawTime);
        }

        // If show_date contains a time component, prefer it
        if (preg_match('/\d{2}:\d{2}(:\d{2})?/', $rawDate)) {
            return Carbon::parse($rawDate);
        }

        // Otherwise combine date + time (this handles show_date='2025-11-27' and show_time='14:30:00')
        return Carbon::parse(trim($rawDate . ' ' . $rawTime));
    }

    /**
     * Allow $showtime->startDateTime as an accessor alias (keeps call sites concise).
     */
    public function getStartDateTimeAttribute()
    {
        return $this->getStartDateTime();
    }
}
