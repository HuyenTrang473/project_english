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
        Schema::create('bai_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('users');
            $table->foreignId('id_lesson')->constrained('lessons');
            $table->string('ten_bai_test');
            $table->text('mo_ta')->nullable();
            $table->integer('thoi_gian_toi_da')->default(60); // phút
            $table->decimal('diem_tong_max', 5, 2)->default(100.00);
            $table->integer('trang_thai')->default(1); // 1: draft, 2: published
            $table->timestamps();

            $table->index(['id_giao_vien', 'id_lesson', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_tests');
    }
};
