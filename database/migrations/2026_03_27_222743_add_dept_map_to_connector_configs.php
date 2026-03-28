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
            if (! Schema::hasColumn('connector_configs', 'dept_map')) {
                $table->json('dept_map')->nullable()->after('user_dept_col')
                    ->comment('UCM department name → external dept code, e.g. {"HR Department":"HR","IT Division":"ITDEV"}');
            }
        });
    }

    public function down(): void
    {
        Schema::table('connector_configs', function (Blueprint $table) {
            if (Schema::hasColumn('connector_configs', 'dept_map')) {
                $table->dropColumn('dept_map');
            }
        });
    }
};
