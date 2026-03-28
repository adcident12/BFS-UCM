<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            // Feature A: Multi-table user JOIN
            // JSON array: [{table, alias, join_type, join_local_col, join_remote_col}, ...]
            // First entry = primary (FROM) table; subsequent entries = JOIN clauses.
            // When set, takes precedence over user_table for SELECT/JOIN queries.
            $table->json('user_tables')->nullable()->after('user_table');

            // Feature B: Mixed permission mode (junction + column simultaneously)
            // perm_col_* fields describe the "column side" of mixed mode.
            // Permission keys from the column side carry the prefix "col:" to distinguish them.
            $table->string('perm_col_table', 100)->nullable()->after('manual_permissions');
            $table->string('perm_col_identifier', 100)->nullable()->after('perm_col_table');
            $table->string('perm_col_value_col', 100)->nullable()->after('perm_col_identifier');
            $table->json('perm_col_options')->nullable()->after('perm_col_value_col');
        });
    }

    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn(['user_tables', 'perm_col_table', 'perm_col_identifier', 'perm_col_value_col', 'perm_col_options']);
        });
    }
};
