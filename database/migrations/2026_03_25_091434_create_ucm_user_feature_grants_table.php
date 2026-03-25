<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ucm_user_feature_grants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('feature_key');
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('ucm_users')->cascadeOnDelete();
            $table->foreign('granted_by')->references('id')->on('ucm_users')->nullOnDelete();
            $table->unique(['user_id', 'feature_key']);
            $table->index('feature_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ucm_user_feature_grants');
    }
};
