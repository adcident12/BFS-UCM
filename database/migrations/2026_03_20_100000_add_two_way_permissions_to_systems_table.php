<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->boolean('two_way_permissions')->default(false)->after('is_active')
                ->comment('แสดง badge 2-way และเตือนว่าการเพิ่ม/ลบ permission มีผลต่อระบบภายนอกด้วย');
        });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->dropColumn('two_way_permissions');
        });
    }
};
