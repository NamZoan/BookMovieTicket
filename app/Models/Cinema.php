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

    // Các trường không được gán đại trà (protected)
    protected $guarded = [];
    public function screens()
    {
        return $this->hasMany(Screen::class, 'cinema_id');
    }
}
