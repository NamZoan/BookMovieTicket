<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->bigIncrements('booking_seat_id');

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('seat_id');

            $table->decimal('seat_price', 10, 2);

            $table->unique(['booking_id', 'seat_id'], 'unique_booking_seat');

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('seat_id')->references('seat_id')->on('seats')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['seat_id']);
        });
        Schema::dropIfExists('booking_seats');
    }
};
