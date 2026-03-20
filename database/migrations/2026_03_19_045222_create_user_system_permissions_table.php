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
        Schema::create('user_system_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('ucm_users')->cascadeOnDelete();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('permission_key');
            $table->foreignId('granted_by')->nullable()->constrained('ucm_users')->nullOnDelete();
            $table->timestamp('granted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'system_id', 'permission_key']);
            $table->index(['user_id', 'system_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_system_permissions');
    }
};
