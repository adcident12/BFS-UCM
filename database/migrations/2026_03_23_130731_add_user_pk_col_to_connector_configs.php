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
        Schema::table('connector_configs', function (Blueprint $table) {
            // คอลัมน์ PK ของ user_table (เช่น 'id')
            // ถ้าตั้งค่า → perm_user_fk_col ใน junction table เก็บค่า PK นี้ ไม่ใช่ identifier โดยตรง
            // DynamicAdapter จะ lookup PK ก่อน แล้วใช้ค่านั้นใน DELETE/INSERT
            $table->string('user_pk_col', 100)->nullable()->after('user_identifier_col');
        });
    }

    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn('user_pk_col');
        });
    }
};
