<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id('movie_id');  // movie_id là khóa chính và tự tăng
            $table->string('title', 200);  // Tiêu đề phim (200 ký tự)
            $table->string('original_title', 200)->nullable();  // Tên gốc của phim
            $table->text('description')->nullable();  // Mô tả phim
            $table->integer('duration');  // Thời gian dài của bộ phim (phút)
            $table->date('release_date')->nullable();  // Ngày phát hành
            $table->string('director', 100)->nullable();  // Đạo diễn phim
            $table->text('cast')->nullable();  // Diễn viên chính
            $table->string('genre', 100)->nullable();  // Thể loại phim
            $table->string('language', 50)->nullable();  // Ngôn ngữ phim
            $table->string('country', 50)->nullable();  // Quốc gia sản xuất
            $table->decimal('rating', 2, 1)->nullable();  // Điểm đánh giá của phim (1.0-10.0)
            $table->string('age_rating', 10)->nullable();  // Phân loại độ tuổi (P, T13, T16...)
            $table->string('poster_url', 500)->nullable();  // URL của poster
            $table->string('trailer_url', 500)->nullable();  // URL của trailer
            $table->enum('status', ['Coming Soon', 'Now Showing', 'Ended'])->default('Coming Soon');  // Trạng thái phim
            $table->timestamps();  // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
