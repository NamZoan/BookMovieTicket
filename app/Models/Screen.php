<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    use HasFactory;

    // Tên bảng nếu khác mặc định (bảng mặc định theo tên số nhiều của model)
    protected $table = 'screens';

    // Khóa chính
    protected $primaryKey = 'screen_id';

    // Các trường có thể được gán đại trà (mass assignable)
    protected $fillable = [
        'cinema_id',
        'screen_name',
        'total_seats'
    ];

    // Quan hệ với bảng cinemas (mỗi phòng chiếu thuộc một rạp chiếu)
    public function cinema()
    {
        return $this->belongsTo(Cinema::class, 'cinema_id');
    }
    public function seats()
    {
        return $this->hasMany(Seat::class, 'screen_id');
    }
    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'screen_id');
    }
}
