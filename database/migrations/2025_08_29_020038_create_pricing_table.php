<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('pricing', function (Blueprint $table) {
        $table->id('pricing_id');  // Tạo cột pricing_id làm khóa chính với kiểu dữ liệu auto increment
        $table->enum('seat_type', ['Normal', 'VIP', 'Couple', 'Disabled']);  // Enum cho seat_type
        $table->enum('day_type', ['Weekday', 'Weekend', 'Holiday']);  // Enum cho day_type
        $table->enum('time_slot', ['Morning', 'Afternoon', 'Evening', 'Late Night']);  // Enum cho time_slot
        $table->decimal('price_multiplier', 3, 2)->default(1.00);  // Cột price_multiplier với giá trị mặc định là 1.00
        $table->timestamps();  // Tạo các cột created_at và updated_at tự động

        // Nếu bạn muốn tạo cột `created_at` là một timestamp cụ thể, có thể thêm:
        // $table->timestamp('created_at')->useCurrent();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
