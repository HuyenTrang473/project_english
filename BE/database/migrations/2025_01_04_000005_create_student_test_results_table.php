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
        Schema::create('student_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hoc_sinh')->constrained('users');
            $table->foreignId('id_bai_test')->constrained('bai_tests');
            $table->decimal('diem_tong', 5, 2)->nullable();
            $table->integer('trang_thai')->default(0); // 0: chưa làm, 1: đang làm, 2: đã nộp, 3: chấm xong
            $table->timestamp('thoi_gian_bat_dau')->nullable();
            $table->timestamp('thoi_gian_ket_thuc')->nullable();
            $table->timestamps();

            $table->unique(['id_hoc_sinh', 'id_bai_test']);
            $table->index(['id_bai_test', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_test_results');
    }
};
