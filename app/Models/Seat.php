<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    // Tên bảng nếu khác mặc định (bảng mặc định là tên số nhiều của model)
    protected $table = 'seats';

    // Khóa chính
    protected $primaryKey = 'seat_id';

    // Các trường có thể được gán đại trà (mass assignable)
    protected $fillable = [
        'screen_id',
        'row_name',
        'seat_number',
        'seat_type'
    ];

    // Quan hệ với bảng screens (mỗi ghế thuộc một phòng chiếu)
    public function screen()
    {
        return $this->belongsTo(Screen::class, 'screen_id');
    }
}
