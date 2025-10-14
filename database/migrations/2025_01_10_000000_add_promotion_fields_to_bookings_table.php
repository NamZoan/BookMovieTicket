<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm các trường cho promotion và tính toán giá
            $table->string('promotion_code', 20)->nullable()->after('total_amount');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('promotion_code');
            $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            
            // Thêm trường cho thông tin người đặt
            $table->string('customer_name', 191)->nullable()->after('user_id');
            $table->string('customer_phone', 20)->nullable()->after('customer_name');
            $table->string('customer_email', 100)->nullable()->after('customer_phone');
            
            // Cập nhật booking_status để hỗ trợ trạng thái mới
            $table->enum('booking_status', ['Pending', 'Confirmed', 'Cancelled', 'Used', 'Expired'])
                  ->default('Pending')
                  ->change();
            
            // Thêm trường cho idempotency key để tránh duplicate payment
            $table->string('idempotency_key', 64)->nullable()->unique()->after('booking_code');
            
            // Thêm trường cho thời gian hết hạn booking
            $table->timestamp('expires_at')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'promotion_code',
                'discount_amount', 
                'final_amount',
                'customer_name',
                'customer_phone',
                'customer_email',
                'idempotency_key',
                'expires_at'
            ]);
            
            // Khôi phục booking_status cũ
            $table->enum('booking_status', ['Confirmed','Cancelled','Used'])
                  ->default('Confirmed')
                  ->change();
        });
    }
};
