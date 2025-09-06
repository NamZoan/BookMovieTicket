<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    // Đặt tên bảng nếu không tuân theo quy tắc Laravel mặc định
    protected $table = 'movies';

    protected $primaryKey = 'movie_id';

    // Các cột có thể được gán giá trị mass-assignment
    protected $fillable = [
        'title',
        'original_title',
        'description',
        'duration',
        'release_date',
        'director',
        'cast',
        'genre',
        'language',
        'country',
        'rating',
        'age_rating',
        'poster_url',
        'trailer_url',
        'status'
    ];

    // Để tránh việc tự động gán giá trị cho created_at và updated_at
    // nếu bạn muốn tự quản lý thời gian, bỏ qua các cột này
    public $timestamps = true;

    // Nếu bạn muốn tự động định dạng `release_date` dưới dạng Carbon
    protected $dates = ['release_date'];

    // Nếu bạn có những thuộc tính đặc biệt, bạn có thể định nghĩa phương thức accessor hoặc mutator
    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id');
    }
}
