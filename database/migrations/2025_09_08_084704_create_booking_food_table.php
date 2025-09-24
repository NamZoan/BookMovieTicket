<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_food', function (Blueprint $table) {
            $table->bigIncrements('booking_food_id');

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('item_id');

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            // Index gợi ý để tối ưu truy vấn
            $table->index(['booking_id', 'item_id']);

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('food_items')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_food', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('booking_food');
    }
};
