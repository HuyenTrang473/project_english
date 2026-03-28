<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Chuyển cột trang_thai từ integer sang string để hỗ trợ giá trị:
     * 'in_progress', 'completed', 'pending_review', 'grading', 'not_started'
     */
    public function up(): void
    {
        // First, drop the unique constraint that might conflict
        // Then change the column type
        Schema::table('student_test_results', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('not_started')->change();
        });

        // Map old integer values to new string values
        DB::table('student_test_results')->where('trang_thai', '0')->update(['trang_thai' => 'not_started']);
        DB::table('student_test_results')->where('trang_thai', '1')->update(['trang_thai' => 'in_progress']);
        DB::table('student_test_results')->where('trang_thai', '2')->update(['trang_thai' => 'completed']);
        DB::table('student_test_results')->where('trang_thai', '3')->update(['trang_thai' => 'grading']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map string values back to integers first
        DB::table('student_test_results')->where('trang_thai', 'not_started')->update(['trang_thai' => '0']);
        DB::table('student_test_results')->where('trang_thai', 'in_progress')->update(['trang_thai' => '1']);
        DB::table('student_test_results')->where('trang_thai', 'completed')->update(['trang_thai' => '2']);
        DB::table('student_test_results')->where('trang_thai', 'grading')->update(['trang_thai' => '3']);
        DB::table('student_test_results')->where('trang_thai', 'pending_review')->update(['trang_thai' => '3']);

        Schema::table('student_test_results', function (Blueprint $table) {
            $table->integer('trang_thai')->default(0)->change();
        });
    }
};
