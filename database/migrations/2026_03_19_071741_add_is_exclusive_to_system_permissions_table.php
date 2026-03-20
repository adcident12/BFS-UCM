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
        Schema::table('system_permissions', function (Blueprint $table) {
            // is_exclusive = true → permissions ใน group เดียวกัน เลือกได้แค่ 1 (radio)
            // is_exclusive = false → เลือกได้หลายอัน (checkbox)
            $table->boolean('is_exclusive')->default(false)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('system_permissions', function (Blueprint $table) {
            $table->dropColumn('is_exclusive');
        });
    }
};
