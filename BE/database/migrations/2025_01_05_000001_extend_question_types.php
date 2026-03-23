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
        // Extend CauHoi table with new question type support
        Schema::table('cau_hois', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('cau_hois', 'loai_cau_hoi')) {
                $table->string('loai_cau_hoi')->default('multiple_choice')->after('noi_dung');
                // Types: multiple_choice, essay, matching, fill_blank, true_false, image_choice
            }

            if (!Schema::hasColumn('cau_hois', 'mo_ta_chi_tiet')) {
                $table->text('mo_ta_chi_tiet')->nullable()->after('noi_dung');
            }

            if (!Schema::hasColumn('cau_hois', 'ghi_chu')) {
                $table->text('ghi_chu')->nullable()->after('mo_ta_chi_tiet');
            }

            if (!Schema::hasColumn('cau_hois', 'hinh_anh_url')) {
                $table->string('hinh_anh_url')->nullable()->after('ghi_chu');
            }
        });

        // Extend DapAn table
        Schema::table('dap_ans', function (Blueprint $table) {
            if (!Schema::hasColumn('dap_ans', 'diem_tu_dong')) {
                $table->float('diem_tu_dong')->default(0)->after('la_dap_an_dung');
                // Điểm tự động nếu trả lời đúng
            }

            if (!Schema::hasColumn('dap_ans', 'hinh_anh_url')) {
                $table->string('hinh_anh_url')->nullable()->after('diem_tu_dong');
            }

            if (!Schema::hasColumn('dap_ans', 'mo_ta_chi_tiet')) {
                $table->text('mo_ta_chi_tiet')->nullable()->after('hinh_anh_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            if (Schema::hasColumn('cau_hois', 'loai_cau_hoi')) {
                $table->dropColumn('loai_cau_hoi');
            }
            if (Schema::hasColumn('cau_hois', 'mo_ta_chi_tiet')) {
                $table->dropColumn('mo_ta_chi_tiet');
            }
            if (Schema::hasColumn('cau_hois', 'ghi_chu')) {
                $table->dropColumn('ghi_chu');
            }
            if (Schema::hasColumn('cau_hois', 'hinh_anh_url')) {
                $table->dropColumn('hinh_anh_url');
            }
        });

        Schema::table('dap_ans', function (Blueprint $table) {
            if (Schema::hasColumn('dap_ans', 'diem_tu_dong')) {
                $table->dropColumn('diem_tu_dong');
            }
            if (Schema::hasColumn('dap_ans', 'hinh_anh_url')) {
                $table->dropColumn('hinh_anh_url');
            }
            if (Schema::hasColumn('dap_ans', 'mo_ta_chi_tiet')) {
                $table->dropColumn('mo_ta_chi_tiet');
            }
        });
    }
};
