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
        // Extend BaiTest table for advanced settings
        Schema::table('bai_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('bai_tests', 'so_lan_lam_toi_da')) {
                $table->integer('so_lan_lam_toi_da')->default(1)->after('diem_tong_max');
                // 0 = unlimited, 1 = làm 1 lần, etc
            }

            if (!Schema::hasColumn('bai_tests', 'co_xao_tron_cau_hoi')) {
                $table->boolean('co_xao_tron_cau_hoi')->default(false)->after('so_lan_lam_toi_da');
                // Shuffle questions for each student
            }

            if (!Schema::hasColumn('bai_tests', 'co_xao_tron_dap_an')) {
                $table->boolean('co_xao_tron_dap_an')->default(false)->after('co_xao_tron_cau_hoi');
                // Shuffle answers for each question
            }

            if (!Schema::hasColumn('bai_tests', 'hien_thi_ket_qua_ngay_lap')) {
                $table->boolean('hien_thi_ket_qua_ngay_lap')->default(true)->after('co_xao_tron_dap_an');
                // Show result immediately after submit
            }

            if (!Schema::hasColumn('bai_tests', 'hien_thi_dap_an_dung')) {
                $table->boolean('hien_thi_dap_an_dung')->default(true)->after('hien_thi_ket_qua_ngay_lap');
                // Show correct answers after submit
            }

            if (!Schema::hasColumn('bai_tests', 'cho_xem_lai_test')) {
                $table->boolean('cho_xem_lai_test')->default(true)->after('hien_thi_dap_an_dung');
                // Allow student to review test after submit
            }

            if (!Schema::hasColumn('bai_tests', 'ngay_bat_dau')) {
                $table->timestamp('ngay_bat_dau')->nullable()->after('cho_xem_lai_test');
                // Start date for test availability
            }

            if (!Schema::hasColumn('bai_tests', 'ngay_ket_thuc')) {
                $table->timestamp('ngay_ket_thuc')->nullable()->after('ngay_bat_dau');
                // End date for test availability
            }
        });

        // Extend StudentTestResult table
        Schema::table('student_test_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_test_results', 'lan_thu')) {
                $table->integer('lan_thu')->default(1)->after('id_bai_test');
                // Attempt number
            }

            if (!Schema::hasColumn('student_test_results', 'thoi_gian_bat_dau')) {
                $table->timestamp('thoi_gian_bat_dau')->nullable()->after('lan_thu');
                // Start timestamp
            }

            if (!Schema::hasColumn('student_test_results', 'thoi_gian_hoan_thanh')) {
                $table->timestamp('thoi_gian_hoan_thanh')->nullable()->after('thoi_gian_bat_dau');
                // Complete timestamp
            }

            if (!Schema::hasColumn('student_test_results', 'thoi_gian_su_dung')) {
                $table->integer('thoi_gian_su_dung')->default(0)->after('thoi_gian_hoan_thanh');
                // Duration in seconds
            }

            if (!Schema::hasColumn('student_test_results', 'so_cau_dung')) {
                $table->integer('so_cau_dung')->default(0)->after('thoi_gian_su_dung');
                // Correct answers count
            }

            if (!Schema::hasColumn('student_test_results', 'so_cau_sai')) {
                $table->integer('so_cau_sai')->default(0)->after('so_cau_dung');
                // Wrong answers count
            }

            if (!Schema::hasColumn('student_test_results', 'so_cau_bo_trong')) {
                $table->integer('so_cau_bo_trong')->default(0)->after('so_cau_sai');
                // Unanswered count
            }

            if (!Schema::hasColumn('student_test_results', 'trang_thai')) {
                $table->string('trang_thai')->default('completed')->after('so_cau_bo_trong');
                // completed, pending_review, grading
            }

            if (!Schema::hasColumn('student_test_results', 'ghi_chu_giao_vien')) {
                $table->text('ghi_chu_giao_vien')->nullable()->after('trang_thai');
                // Teacher notes/feedback
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_tests', function (Blueprint $table) {
            if (Schema::hasColumn('bai_tests', 'so_lan_lam_toi_da')) {
                $table->dropColumn('so_lan_lam_toi_da');
            }
            if (Schema::hasColumn('bai_tests', 'co_xao_tron_cau_hoi')) {
                $table->dropColumn('co_xao_tron_cau_hoi');
            }
            if (Schema::hasColumn('bai_tests', 'co_xao_tron_dap_an')) {
                $table->dropColumn('co_xao_tron_dap_an');
            }
            if (Schema::hasColumn('bai_tests', 'hien_thi_ket_qua_ngay_lap')) {
                $table->dropColumn('hien_thi_ket_qua_ngay_lap');
            }
            if (Schema::hasColumn('bai_tests', 'hien_thi_dap_an_dung')) {
                $table->dropColumn('hien_thi_dap_an_dung');
            }
            if (Schema::hasColumn('bai_tests', 'cho_xem_lai_test')) {
                $table->dropColumn('cho_xem_lai_test');
            }
            if (Schema::hasColumn('bai_tests', 'ngay_bat_dau')) {
                $table->dropColumn('ngay_bat_dau');
            }
            if (Schema::hasColumn('bai_tests', 'ngay_ket_thuc')) {
                $table->dropColumn('ngay_ket_thuc');
            }
        });

        Schema::table('student_test_results', function (Blueprint $table) {
            if (Schema::hasColumn('student_test_results', 'lan_thu')) {
                $table->dropColumn('lan_thu');
            }
            if (Schema::hasColumn('student_test_results', 'thoi_gian_bat_dau')) {
                $table->dropColumn('thoi_gian_bat_dau');
            }
            if (Schema::hasColumn('student_test_results', 'thoi_gian_hoan_thanh')) {
                $table->dropColumn('thoi_gian_hoan_thanh');
            }
            if (Schema::hasColumn('student_test_results', 'thoi_gian_su_dung')) {
                $table->dropColumn('thoi_gian_su_dung');
            }
            if (Schema::hasColumn('student_test_results', 'so_cau_dung')) {
                $table->dropColumn('so_cau_dung');
            }
            if (Schema::hasColumn('student_test_results', 'so_cau_sai')) {
                $table->dropColumn('so_cau_sai');
            }
            if (Schema::hasColumn('student_test_results', 'so_cau_bo_trong')) {
                $table->dropColumn('so_cau_bo_trong');
            }
            if (Schema::hasColumn('student_test_results', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
            if (Schema::hasColumn('student_test_results', 'ghi_chu_giao_vien')) {
                $table->dropColumn('ghi_chu_giao_vien');
            }
        });
    }
};
