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
        Schema::table('showtimes', function (Blueprint $table) {
            // Xóa cột base_price
            $table->dropColumn('base_price');

            // Thêm các cột mới cho giá vé
            $table->decimal('price_seat_normal', 10, 2)->default(0.00);  // Giá vé loại thường
            $table->decimal('price_seat_vip', 10, 2)->default(0.00);     // Giá vé loại VIP
            $table->decimal('price_seat_couple', 10, 2)->default(0.00);  // Giá vé loại cặp
        });
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            // Khôi phục lại cột base_price
            $table->decimal('base_price', 10, 2);

            // Xóa các cột đã thêm
            $table->dropColumn('price_seat_normal');
            $table->dropColumn('price_seat_vip');
            $table->dropColumn('price_seat_couple');
        });
    }
};
