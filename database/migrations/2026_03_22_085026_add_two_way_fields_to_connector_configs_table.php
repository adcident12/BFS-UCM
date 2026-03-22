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
            // ── 2-Way Sync: Permission Definition Table ──────────────────────
            $table->string('perm_def_table', 100)->nullable()->after('perm_group_col');
            $table->string('perm_def_value_col', 100)->nullable()->after('perm_def_table');
            $table->string('perm_def_pk_col', 100)->nullable()->after('perm_def_value_col');
            $table->string('perm_def_label_col', 100)->nullable()->after('perm_def_pk_col');
            $table->string('perm_def_group_col', 100)->nullable()->after('perm_def_label_col');
            $table->string('perm_delete_mode', 20)->nullable()->after('perm_def_group_col');
            $table->string('perm_def_soft_delete_col', 100)->nullable()->after('perm_delete_mode');
            $table->string('perm_def_soft_delete_val', 100)->nullable()->after('perm_def_soft_delete_col');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn([
                'perm_def_table',
                'perm_def_value_col',
                'perm_def_pk_col',
                'perm_def_label_col',
                'perm_def_group_col',
                'perm_delete_mode',
                'perm_def_soft_delete_col',
                'perm_def_soft_delete_val',
            ]);
        });
    }
};
