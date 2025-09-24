<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // bảng không có created_at/updated_at

    protected $fillable = [
        'user_id', 'showtime_id', 'booking_code',
        'total_amount', 'discount_amount', 'final_amount',
        'payment_method', 'payment_status', 'booking_status',
        'booking_date', 'payment_date', 'notes',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'booking_date'    => 'datetime',
        'payment_date'    => 'datetime',
    ];

    // Quan hệ
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class, 'showtime_id', 'showtime_id');
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class, 'booking_id', 'booking_id');
    }

    public function seats() // tiện dùng pivot để lấy ghế trực tiếp
    {
        return $this->belongsToMany(Seat::class, 'booking_seats', 'booking_id', 'seat_id')
                    ->withPivot('seat_price');
    }

    public function bookingFoods()
    {
        return $this->hasMany(BookingFood::class, 'booking_id', 'booking_id');
    }

    public function foods()
    {
        return $this->belongsToMany(FoodItem::class, 'booking_food', 'booking_id', 'item_id')
                    ->withPivot(['quantity', 'unit_price', 'total_price']);
    }
}
