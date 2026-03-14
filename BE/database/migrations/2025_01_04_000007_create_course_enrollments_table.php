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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hoc_sinh')->constrained('users');
            $table->foreignId('id_lesson')->constrained('lessons');
            $table->timestamp('ngay_dang_ky');
            $table->timestamps();

            $table->unique(['id_hoc_sinh', 'id_lesson']);
            $table->index(['id_lesson', 'ngay_dang_ky']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
