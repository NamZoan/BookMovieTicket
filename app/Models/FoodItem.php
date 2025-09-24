<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $table = 'food_items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Bảng chỉ có created_at, không có updated_at
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'is_available',
        'created_at',
    ];

    // Để tiện dùng danh sách category trong form
    public const CATEGORIES = ['Popcorn', 'Drinks', 'Snacks', 'Combo'];

    // Casts
    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
        'created_at'   => 'datetime',
    ];

    public function bookingFoods()
    {
        return $this->hasMany(BookingFood::class, 'item_id', 'item_id');
    }
}
