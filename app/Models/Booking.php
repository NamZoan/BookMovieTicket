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
        'user_id', 'showtime_id', 'booking_code', 'idempotency_key',
        'customer_name', 'customer_phone', 'customer_email',
        'total_amount', 'promotion_code', 'discount_amount', 'final_amount',
        'payment_method', 'payment_status', 'booking_status',
        'booking_date', 'payment_date', 'expires_at', 'notes',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'booking_date'    => 'datetime',
        'payment_date'    => 'datetime',
        'expires_at'      => 'datetime',
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

    // Quan hệ với promotion
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_code', 'code');
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->whereIn('booking_status', ['Pending', 'Confirmed']);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('payment_status', 'Pending');
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPaid()
    {
        return $this->payment_status === 'Paid';
    }

    public function canCancel()
    {
        return $this->payment_status === 'Pending' && !$this->isExpired();
    }

    // Tính toán giá
    public function getSubtotalAttribute()
    {
        return $this->total_amount;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->total_amount > 0) {
            return round(($this->discount_amount / $this->total_amount) * 100, 2);
        }
        return 0;
    }
}
