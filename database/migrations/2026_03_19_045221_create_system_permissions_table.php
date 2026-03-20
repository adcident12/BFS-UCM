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
        Schema::create('system_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('key');             // เช่น 'edit_repair', 'view_report'
            $table->string('label');           // ชื่อแสดงใน UI เช่น 'แก้ไขงานซ่อม'
            $table->string('group')->nullable(); // กลุ่ม เช่น 'งานซ่อม', 'รายงาน'
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['system_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_permissions');
    }
};
