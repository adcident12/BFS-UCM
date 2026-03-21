<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connector_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();

            // ── Database Connection ────────────────────────────────────────
            $table->string('db_driver', 20)->default('mysql'); // mysql | pgsql | sqlsrv
            $table->string('db_host', 255);
            $table->unsignedSmallInteger('db_port')->default(3306);
            $table->string('db_name', 100);
            $table->string('db_user', 100);
            $table->text('db_password');

            // ── User Table Mapping ─────────────────────────────────────────
            $table->string('user_table', 100);
            $table->string('user_ucm_identifier', 20)->default('username'); // 'username' | 'employee_number'
            $table->string('user_identifier_col', 100);   // remote column that matches ucm identifier
            $table->string('user_name_col', 100)->nullable();
            $table->string('user_email_col', 100)->nullable();
            $table->string('user_dept_col', 100)->nullable();
            $table->string('user_status_col', 100)->nullable();
            $table->string('user_status_active_val', 100)->nullable(); // e.g. '1', 'active', 'Y'

            // ── Permission Mode ────────────────────────────────────────────
            // 'junction' : separate junction table (user_fk → perm_value)
            // 'column'   : single column on user_table (value = permission key)
            // 'manual'   : static list, no auto-sync from remote
            $table->string('permission_mode', 20)->default('junction');

            // ── Permission Table Mapping (junction / column modes) ─────────
            $table->string('perm_table', 100)->nullable();          // junction table name
            $table->string('perm_user_fk_col', 100)->nullable();    // FK → user identifier
            $table->string('perm_value_col', 100)->nullable();      // the permission value col
            $table->string('perm_label_col', 100)->nullable();      // human-readable label col
            $table->string('perm_group_col', 100)->nullable();      // grouping col

            // ── Manual Permissions (manual mode) ──────────────────────────
            $table->json('manual_permissions')->nullable(); // [{'key':'x','label':'X','group':'G'}, ...]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connector_configs');
    }
};
