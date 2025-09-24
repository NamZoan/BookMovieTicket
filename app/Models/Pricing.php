<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    // Định nghĩa bảng
    protected $table = 'pricing';
    protected $primaryKey = 'pricing_id';

    // Định nghĩa các trường có thể gán đại trà
    protected $fillable = [
        'seat_type',
        'day_type',
        'time_slot',
        'price_multiplier',
    ];

    // Nếu bạn muốn sử dụng kiểu dữ liệu timestamp tự động
    public $timestamps = true;

    // Accessor để lấy tên tiếng Việt của loại ghế
    public function getSeatTypeNameAttribute()
    {
        return match($this->seat_type) {
            'Normal' => 'Thường',
            'VIP' => 'VIP',
            'Couple' => 'Đôi',
            default => $this->seat_type,
        };
    }

    // Accessor để lấy tên tiếng Việt của loại ngày
    public function getDayTypeNameAttribute(): mixed
    {
        return match($this->day_type) {
            'Weekday' => 'Ngày thường',
            'Weekend' => 'Cuối tuần',
            'Holiday' => 'Ngày lễ',
            default => $this->day_type,
        };
    }

    // Accessor để lấy tên tiếng Việt của khung giờ
    public function getTimeSlotNameAttribute()
    {
        return match($this->time_slot) {
            'Morning' => 'Sáng',
            'Afternoon' => 'Chiều',
            'Evening' => 'Tối',
            'Late Night' => 'Đêm khuya',
            default => $this->time_slot,
        };
    }

    // Method để lấy pricing theo điều kiện
    public static function getPricingByConditions($seatType, $dayType, $timeSlot)
    {
        return static::where('seat_type', $seatType)
                    ->where('day_type', $dayType)
                    ->where('time_slot', $timeSlot)
                    ->first();
    }

    // Method để tính giá vé dựa trên giá gốc
    public function calculatePrice($basePrice)
    {
        return $basePrice * $this->price_multiplier;
    }

    // Scope để lọc theo loại ghế
    public function scopeBySeatType($query, $seatType)
    {
        return $query->where('seat_type', $seatType);
    }

    // Scope để lọc theo loại ngày
    public function scopeByDayType($query, $dayType)
    {
        return $query->where('day_type', $dayType);
    }

    // Scope để lọc theo khung giờ
    public function scopeByTimeSlot($query, $timeSlot)
    {
        return $query->where('time_slot', $timeSlot);
    }
}
