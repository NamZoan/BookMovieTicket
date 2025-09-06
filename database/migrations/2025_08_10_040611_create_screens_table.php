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
    { {
            Schema::create('screens', function (Blueprint $table) {
                $table->id('screen_id'); // Khóa chính, tự động tăng
                $table->unsignedBigInteger('cinema_id'); // Sửa lại cột cinema_id thành unsignedBigInteger
                $table->string('screen_name', 50); // Tên phòng chiếu
                $table->integer('total_seats'); // Tổng số ghế
                $table->timestamps(); // created_at, updated_at

                // Tạo khóa ngoại liên kết với bảng cinemas
                $table->foreign('cinema_id')->references('cinema_id')->on('cinemas')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
