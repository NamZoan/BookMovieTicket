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
        Schema::create('seats', function (Blueprint $table) {
            $table->id('seat_id'); // Khóa chính, tự động tăng
            $table->unsignedBigInteger('screen_id'); // Khóa ngoại tham chiếu tới bảng screens
            $table->string('row_name', 5); // Tên hàng ghế (A, B, C, v.v.)
            $table->integer('seat_number'); // Số ghế trong hàng
            $table->enum('seat_type', ['Normal','VIP','Couple','Disabled'])->default('Normal'); // Loại ghế
            $table->timestamps(); // created_at, updated_at

            // Khóa ngoại liên kết với bảng screens
            $table->foreign('screen_id')->references('screen_id')->on('screens')->onDelete('cascade');

            // Chỉ mục duy nhất cho screen_id, row_name và seat_number
            $table->unique(['screen_id', 'row_name', 'seat_number'], 'unique_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
