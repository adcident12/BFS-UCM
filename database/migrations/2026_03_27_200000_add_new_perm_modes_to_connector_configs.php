<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            // --- Mode: json_column ---
            // permissions เก็บเป็น JSON array ใน 1 column บน user table
            // เช่น: permissions = '["read","write","approve"]'
            if (! Schema::hasColumn('connector_configs', 'perm_json_col')) {
                $table->string('perm_json_col', 100)->nullable()->after('perm_insert_meta_cols');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_json_available')) {
                $table->json('perm_json_available')->nullable()->after('perm_json_col');
            }

            // --- Mode: delimited_column ---
            // permissions เก็บเป็น string คั่นด้วย delimiter ใน 1 column บน user table
            // เช่น: role = 'admin,editor,viewer' หรือ roles = 'HR|Finance'
            if (! Schema::hasColumn('connector_configs', 'perm_delimited_col')) {
                $table->string('perm_delimited_col', 100)->nullable()->after('perm_json_available');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_delimiter')) {
                $table->string('perm_delimiter', 10)->nullable()->default(',')->after('perm_delimited_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_delimited_available')) {
                $table->json('perm_delimited_available')->nullable()->after('perm_delimiter');
            }

            // --- Mode: bitmask ---
            // permissions เก็บเป็น integer bit flags บน user table
            // เช่น: perms_flag = 7 (bit 1=read, bit 2=write, bit 4=admin)
            if (! Schema::hasColumn('connector_configs', 'perm_bitmask_col')) {
                $table->string('perm_bitmask_col', 100)->nullable()->after('perm_delimited_available');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_bitmask_map')) {
                // JSON object: {"1":"read","2":"write","4":"admin","8":"delete"}
                $table->json('perm_bitmask_map')->nullable()->after('perm_bitmask_col');
            }

            // --- Mode: multi_level_hierarchy ---
            // RBAC recursive: users → user_roles → roles(parent_id) → role_permissions → permissions
            // ----
            // Membership table (user → role)
            if (! Schema::hasColumn('connector_configs', 'perm_hier_user_role_table')) {
                $table->string('perm_hier_user_role_table', 100)->nullable()->after('perm_bitmask_map');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_user_fk_col')) {
                $table->string('perm_hier_user_fk_col', 80)->nullable()->after('perm_hier_user_role_table');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_role_fk_col')) {
                $table->string('perm_hier_role_fk_col', 80)->nullable()->after('perm_hier_user_fk_col');
            }
            // Roles table (self-referential)
            if (! Schema::hasColumn('connector_configs', 'perm_hier_role_table')) {
                $table->string('perm_hier_role_table', 100)->nullable()->after('perm_hier_role_fk_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_role_pk_col')) {
                $table->string('perm_hier_role_pk_col', 80)->nullable()->after('perm_hier_role_table');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_role_name_col')) {
                $table->string('perm_hier_role_name_col', 80)->nullable()->after('perm_hier_role_pk_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_role_parent_col')) {
                // self-reference FK column (nullable = flat list ไม่มี hierarchy ก็ได้)
                $table->string('perm_hier_role_parent_col', 80)->nullable()->after('perm_hier_role_name_col');
            }
            // Role-permissions table
            if (! Schema::hasColumn('connector_configs', 'perm_hier_perm_table')) {
                $table->string('perm_hier_perm_table', 100)->nullable()->after('perm_hier_role_parent_col');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_perm_role_fk_col')) {
                $table->string('perm_hier_perm_role_fk_col', 80)->nullable()->after('perm_hier_perm_table');
            }
            if (! Schema::hasColumn('connector_configs', 'perm_hier_perm_value_col')) {
                $table->string('perm_hier_perm_value_col', 80)->nullable()->after('perm_hier_perm_role_fk_col');
            }
        });
    }

    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            $table->dropColumn([
                'perm_json_col',
                'perm_json_available',
                'perm_delimited_col',
                'perm_delimiter',
                'perm_delimited_available',
                'perm_bitmask_col',
                'perm_bitmask_map',
                'perm_hier_user_role_table',
                'perm_hier_user_fk_col',
                'perm_hier_role_fk_col',
                'perm_hier_role_table',
                'perm_hier_role_pk_col',
                'perm_hier_role_name_col',
                'perm_hier_role_parent_col',
                'perm_hier_perm_table',
                'perm_hier_perm_role_fk_col',
                'perm_hier_perm_value_col',
            ]);
        });
    }
};
