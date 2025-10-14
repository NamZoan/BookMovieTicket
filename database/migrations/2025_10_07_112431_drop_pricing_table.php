<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xóa bảng 'pricing'
        Schema::dropIfExists('pricing');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nếu muốn phục hồi bảng 'pricing', bạn có thể thêm lại cấu trúc bảng tại đây (nếu cần)
        // Example:
        // Schema::create('pricing', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('price');
        //     $table->timestamps();
        // });
    }
}
