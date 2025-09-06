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
        'base_price',
        'available_seats',
        'status'
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
}
