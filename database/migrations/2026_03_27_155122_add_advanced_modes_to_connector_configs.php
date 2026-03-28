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
            // Scenario I — Boolean Matrix: list of TINYINT permission column names
            if (! Schema::hasColumn('connector_configs', 'perm_bool_columns')) {
                $table->json('perm_bool_columns')->nullable()->after('perm_col_options');
            }

            // Scenario J — Soft-Delete Junction: is_active-style flag column
            if (! Schema::hasColumn('connector_configs', 'perm_junction_active_col')) {
                $table->string('perm_junction_active_col', 80)->nullable()->after('perm_bool_columns');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_junction_active_val')) {
                $table->string('perm_junction_active_val', 20)->nullable()->default('1')->after('perm_junction_active_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_junction_inactive_val')) {
                $table->string('perm_junction_inactive_val', 20)->nullable()->default('0')->after('perm_junction_active_val');
            }

            // Scenario K — Group Inheritance: via (membership) table
            if (! Schema::hasColumn('connector_configs', 'perm_via_table')) {
                $table->string('perm_via_table', 100)->nullable()->after('perm_junction_inactive_val');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_via_user_fk_col')) {
                $table->string('perm_via_user_fk_col', 80)->nullable()->after('perm_via_table');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_via_group_fk_col')) {
                $table->string('perm_via_group_fk_col', 80)->nullable()->after('perm_via_user_fk_col');
            }

            // Scenario L — Time-Bounded Junction: valid_from / valid_to date columns
            if (! Schema::hasColumn('connector_configs', 'perm_valid_from_col')) {
                $table->string('perm_valid_from_col', 80)->nullable()->after('perm_via_group_fk_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_valid_to_col')) {
                $table->string('perm_valid_to_col', 80)->nullable()->after('perm_valid_from_col');
            }

            // Scenario M — Insert Metadata: column → token map (e.g. {"granted_by":"__ucm_admin__","granted_at":"__now__"})
            if (! Schema::hasColumn('connector_configs', 'perm_insert_meta_cols')) {
                $table->json('perm_insert_meta_cols')->nullable()->after('perm_valid_to_col');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn([
                'perm_bool_columns',
                'perm_junction_active_col',
                'perm_junction_active_val',
                'perm_junction_inactive_val',
                'perm_via_table',
                'perm_via_user_fk_col',
                'perm_via_group_fk_col',
                'perm_valid_from_col',
                'perm_valid_to_col',
                'perm_insert_meta_cols',
            ]);
        });
    }
};
