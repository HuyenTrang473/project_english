<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove the unique constraint on (id_hoc_sinh, id_bai_test) because we need to support
     * multiple attempts per student per test with different lan_thu values.
     * Instead, the unique constraint should be on (id_hoc_sinh, id_bai_test, lan_thu)
     */
    public function up(): void
    {
        // Drop and recreate the constraint using raw SQL to avoid foreign key issues
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE `student_test_results`
            DROP INDEX `student_test_results_id_hoc_sinh_id_bai_test_unique`,
            ADD UNIQUE KEY `student_test_results_id_hoc_sinh_id_bai_test_lan_thu_unique` (`id_hoc_sinh`, `id_bai_test`, `lan_thu`),
            ADD INDEX `student_test_results_id_hoc_sinh_trang_thai_index` (`id_hoc_sinh`, `trang_thai`)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_test_results', function (Blueprint $table) {
            // Revert to original constraint
            $table->dropUnique(['id_hoc_sinh', 'id_bai_test', 'lan_thu']);
            $table->dropIndex(['id_hoc_sinh', 'trang_thai']);
            $table->unique(['id_hoc_sinh', 'id_bai_test']);
        });
    }
};
