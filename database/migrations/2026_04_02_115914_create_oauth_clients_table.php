<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->comment('Machine-readable identifier');
            $table->string('client_id', 40)->unique()->comment('Public OAuth client identifier');
            $table->string('client_secret_hash')->nullable()->comment('bcrypt hash; null = public client (PKCE only)');
            $table->json('redirect_uris')->comment('Allowed redirect URIs');
            $table->json('allowed_scopes');
            $table->json('grant_types');
            $table->boolean('is_confidential')->default(true)->comment('Has client_secret');
            $table->boolean('auto_approve')->default(false)->comment('Skip consent screen for trusted apps');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('homepage_url')->nullable();
            $table->foreignId('registered_by')->constrained('ucm_users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
