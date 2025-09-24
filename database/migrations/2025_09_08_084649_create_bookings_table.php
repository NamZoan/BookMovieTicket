<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('booking_id');

            // Khóa ngoại tới users(user_id) & showtimes(showtime_id)
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('showtime_id');

            $table->string('booking_code', 20)->unique();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);

            $table->enum('payment_method', ['Cash','Credit Card','Banking','E-Wallet','Loyalty Points'])->nullable();
            $table->enum('payment_status', ['Pending','Paid','Failed','Refunded'])->default('Pending');
            $table->enum('booking_status', ['Confirmed','Cancelled','Used'])->default('Confirmed');

            $table->timestamp('booking_date')->useCurrent();
            $table->timestamp('payment_date')->nullable();
            $table->text('notes')->nullable();

            // KHÔNG thêm timestamps() vì bảng gốc không yêu cầu created_at/updated_at

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('showtime_id')->references('showtime_id')->on('showtimes')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['showtime_id']);
        });
        Schema::dropIfExists('bookings');
    }
};
