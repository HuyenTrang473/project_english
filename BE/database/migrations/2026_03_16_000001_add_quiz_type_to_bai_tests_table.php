<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột loai_quiz để phân loại các bài quiz
     * loai_quiz: listening, writing, reading, mixed
     */
    public function up(): void
    {
        Schema::table('bai_tests', function (Blueprint $table) {
            // Thêm cột loại quiz - mặc định là 'mixed'
            $table->string('loai_quiz')->default('mixed')->after('ten_bai_test');
            // Mô tả chi tiết loại quiz
            $table->text('chi_tiet_loai_quiz')->nullable()->after('loai_quiz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_tests', function (Blueprint $table) {
            $table->dropColumn(['loai_quiz', 'chi_tiet_loai_quiz']);
        });
    }
};
