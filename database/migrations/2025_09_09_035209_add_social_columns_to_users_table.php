<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nếu password đang NOT NULL, cho phép nullable để user social không cần mật khẩu
            $table->string('password')->nullable()->change();

            $table->string('provider')->nullable()->after('is_active');
            $table->string('provider_id')->nullable()->index()->after('provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id']);
        });
    }
};

