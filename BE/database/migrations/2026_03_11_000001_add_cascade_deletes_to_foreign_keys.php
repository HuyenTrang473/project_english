<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bổ sung onDelete('cascade') cho các foreign key.
     * Khi xoá BaiTest → tự động xoá CauHoi → xoá DapAn
     * Khi xoá StudentTestResult → xoá StudentAnswerDetail
     */
    public function up(): void
    {
        // cau_hois.id_bai_test → cascade
        Schema::table('cau_hois', function (Blueprint $table) {
            $table->dropForeign(['id_bai_test']);
            $table->foreign('id_bai_test')
                ->references('id')->on('bai_tests')
                ->onDelete('cascade');
        });

        // dap_ans.id_cau_hoi → cascade
        Schema::table('dap_ans', function (Blueprint $table) {
            $table->dropForeign(['id_cau_hoi']);
            $table->foreign('id_cau_hoi')
                ->references('id')->on('cau_hois')
                ->onDelete('cascade');
        });

        // student_answer_details.id_student_test_result → cascade
        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_student_test_result']);
            $table->foreign('id_student_test_result')
                ->references('id')->on('student_test_results')
                ->onDelete('cascade');
        });

        // student_answer_details.id_cau_hoi → cascade
        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_cau_hoi']);
            $table->foreign('id_cau_hoi')
                ->references('id')->on('cau_hois')
                ->onDelete('cascade');
        });

        // student_answer_details.id_dap_an → set null khi xoá đáp án
        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_dap_an']);
            $table->foreign('id_dap_an')
                ->references('id')->on('dap_ans')
                ->onDelete('set null');
        });

        // student_test_results.id_bai_test → cascade
        Schema::table('student_test_results', function (Blueprint $table) {
            $table->dropForeign(['id_bai_test']);
            $table->foreign('id_bai_test')
                ->references('id')->on('bai_tests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse — khôi phục FK mặc định (restrict).
     */
    public function down(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            $table->dropForeign(['id_bai_test']);
            $table->foreign('id_bai_test')->references('id')->on('bai_tests');
        });

        Schema::table('dap_ans', function (Blueprint $table) {
            $table->dropForeign(['id_cau_hoi']);
            $table->foreign('id_cau_hoi')->references('id')->on('cau_hois');
        });

        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_student_test_result']);
            $table->foreign('id_student_test_result')->references('id')->on('student_test_results');
        });

        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_cau_hoi']);
            $table->foreign('id_cau_hoi')->references('id')->on('cau_hois');
        });

        Schema::table('student_answer_details', function (Blueprint $table) {
            $table->dropForeign(['id_dap_an']);
            $table->foreign('id_dap_an')->references('id')->on('dap_ans');
        });

        Schema::table('student_test_results', function (Blueprint $table) {
            $table->dropForeign(['id_bai_test']);
            $table->foreign('id_bai_test')->references('id')->on('bai_tests');
        });
    }
};
