<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ucm_users', function (Blueprint $table) {
            // boolean (tinyint 0/1) → tinyint (0=ทั่วไป, 1=admin ระดับ 1, 2=admin ระดับ 2)
            $table->tinyInteger('is_admin')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ucm_users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->change();
        });
    }
};
