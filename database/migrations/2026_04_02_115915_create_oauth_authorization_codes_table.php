<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_authorization_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 128)->unique()->comment('Random authorization code (one-time use)');
            $table->foreignId('client_id')->constrained('oauth_clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('ucm_users')->cascadeOnDelete();
            $table->json('scopes');
            $table->string('redirect_uri');
            $table->timestamp('expires_at')->comment('10 minutes from creation');
            $table->string('code_challenge', 128)->nullable()->comment('PKCE S256 challenge');
            $table->string('code_challenge_method', 10)->nullable()->comment('S256 or plain');
            $table->string('state', 256)->nullable()->comment('Client-provided CSRF state');
            $table->string('nonce', 256)->nullable()->comment('OIDC nonce for replay prevention');
            $table->boolean('is_used')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_authorization_codes');
    }
};
