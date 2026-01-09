<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('loyalty_points');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0);
        });
    }
};
