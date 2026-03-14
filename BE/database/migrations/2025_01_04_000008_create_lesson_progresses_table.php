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
        Schema::create('lesson_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hoc_sinh')->constrained('users');
            $table->foreignId('id_lesson')->constrained('lessons');
            $table->integer('tien_do_phan_tram')->default(0); // 0-100%
            $table->timestamp('thoi_gian_bat_dau')->nullable();
            $table->timestamp('thoi_gian_hoan_thanh')->nullable();
            $table->timestamps();

            $table->unique(['id_hoc_sinh', 'id_lesson']);
            $table->index(['id_lesson', 'tien_do_phan_tram']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progresses');
    }
};
