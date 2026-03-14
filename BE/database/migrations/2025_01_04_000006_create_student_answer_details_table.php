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
        Schema::create('student_answer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_student_test_result')->constrained('student_test_results');
            $table->foreignId('id_cau_hoi')->constrained('cau_hois');
            $table->foreignId('id_dap_an')->nullable()->constrained('dap_ans');
            $table->text('cau_tra_loi_tu_do')->nullable(); // cho loại essay
            $table->decimal('diem_cau_hoi', 5, 2)->nullable();
            $table->boolean('la_dung')->nullable();
            $table->timestamps();

            $table->unique(['id_student_test_result', 'id_cau_hoi']);
            $table->index(['id_cau_hoi', 'la_dung']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answer_details');
    }
};
