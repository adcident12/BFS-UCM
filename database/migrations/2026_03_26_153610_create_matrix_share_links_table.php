<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrix_share_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('label');
            $table->json('filter_system_ids')->nullable();
            $table->json('filter_departments')->nullable();
            $table->json('filter_usernames')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at');
            $table->timestamp('last_viewed_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->foreignId('created_by')->constrained('ucm_users');
            $table->foreignId('revoked_by')->nullable()->constrained('ucm_users');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrix_share_links');
    }
};
