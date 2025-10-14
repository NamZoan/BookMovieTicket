<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm cột mới (nullable để an toàn với dữ liệu cũ)
            $table->string('name', 191)->nullable()->after('user_id');
            $table->string('phone', 20)->nullable()->after('name');

            // Xoá 2 cột tiền
            $table->dropColumn(['final_amount', 'discount_amount']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Khôi phục 2 cột tiền như schema ban đầu
            // discount_amount: DECIMAL(10,2) NOT NULL DEFAULT 0.00
            // final_amount:   DECIMAL(10,2) NOT NULL
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('total_amount');
            $table->decimal('final_amount', 10, 2)->default(0.00)->after('discount_amount');

            // Xoá 2 cột vừa thêm
            $table->dropColumn(['name', 'phone']);
        });

        // Gán giá trị hợp lý sau khi thêm lại cột (tránh NOT NULL bị 0 hết)
        // Ở đây ta set discount = 0 và final = total_amount
        DB::statement('UPDATE bookings SET discount_amount = 0.00, final_amount = total_amount');
    }
};
