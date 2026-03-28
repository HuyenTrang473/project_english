<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thay đổi loai_cau_hoi từ integer sang string để hỗ trợ các loại câu hỏi mới:
     * listening, multiple_choice, true_false, essay, matching, fill_blank, image_choice
     */
    public function up(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            // Change loai_cau_hoi from integer to string
            $table->string('loai_cau_hoi')->default('multiple_choice')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('loai_cau_hoi')->default(1)->change();
        });
    }
};
