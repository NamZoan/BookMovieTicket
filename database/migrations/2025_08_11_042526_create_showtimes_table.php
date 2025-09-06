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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id('showtime_id'); // Khóa chính, tự động tăng
            $table->unsignedBigInteger('movie_id'); // Khóa ngoại liên kết với bảng movies
            $table->unsignedBigInteger('screen_id'); // Khóa ngoại liên kết với bảng screens
            $table->date('show_date'); // Ngày suất chiếu
            $table->time('show_time'); // Thời gian bắt đầu suất chiếu
            $table->time('end_time'); // Thời gian kết thúc suất chiếu
            $table->decimal('base_price', 10, 2); // Giá cơ bản của vé
            $table->integer('available_seats'); // Số ghế còn trống
            $table->enum('status', ['Active', 'Cancelled', 'Full'])->default('Active'); // Trạng thái suất chiếu
            $table->timestamps(); // created_at, updated_at

            // Khóa ngoại liên kết với bảng movies và screens
            $table->foreign('movie_id')->references('movie_id')->on('movies')->onDelete('cascade');
            $table->foreign('screen_id')->references('screen_id')->on('screens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
