<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFood extends Model
{
    protected $table = 'booking_food';
    protected $primaryKey = 'booking_food_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'booking_id', 'item_id', 'quantity',
        'unit_price', 'total_price',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'item_id', 'item_id');
    }
}
