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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('adapter_class')->nullable();       // App\Adapters\RepairSystemAdapter
            $table->string('db_host')->nullable();
            $table->string('db_port')->default('3306');
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->string('api_url')->nullable();
            $table->string('api_token')->nullable();
            $table->string('color', 7)->default('#6366f1'); // สีแสดงใน UI
            $table->string('icon')->default('server');       // heroicon name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
