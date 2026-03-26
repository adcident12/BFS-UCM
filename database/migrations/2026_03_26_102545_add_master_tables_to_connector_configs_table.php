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
            $table->json('master_tables')->nullable()->after('perm_def_soft_delete_val');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn('master_tables');
        });
    }
};
