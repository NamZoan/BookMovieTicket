<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    // Đặt tên bảng nếu khác mặc định (tên bảng mặc định là tên số nhiều của Model)
    protected $table = 'cinemas';

    // Đặt khóa chính nếu khác mặc định (mặc định là 'id')
    protected $primaryKey = 'cinema_id';

    // Các trường có thể được gán đại trà (mass assignable)
    protected $fillable = [
        'name',
        'address',
        'city',
    ];

    protected $casts = [
        'gallery' => 'array',
        'amenities' => 'array',
    ];

    // Scope for search
    public function scopeSearch($query, $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%");
        });
    }

    public function scopeCity($query, $city)
    {
        if (!$city) return $query;
        return $query->where('city', $city);
    }

    // Các trường không được gán đại trà (protected)
    protected $guarded = [];
    public function screens()
    {
        return $this->hasMany(Screen::class, 'cinema_id');
    }
}
