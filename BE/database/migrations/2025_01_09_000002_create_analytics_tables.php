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
        // Create analytics table for test statistics
        Schema::create('test_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bai_test');
            $table->integer('so_hoc_sinh_lam')->default(0);
            $table->float('diem_trung_binh')->default(0);
            $table->float('diem_min')->default(0);
            $table->float('diem_max')->default(0);
            $table->float('ty_le_hoc_sinh_dau')->default(0); // Pass rate
            $table->integer('thoi_gian_trung_binh')->default(0); // Average time in seconds
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_bai_test')->references('id')->on('bai_tests')->onDelete('cascade');
            $table->index('id_bai_test');
        });

        // Create question statistics table
        Schema::create('question_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cau_hoi');
            $table->unsignedBigInteger('id_bai_test');
            $table->integer('so_hoc_sinh_lam')->default(0);
            $table->integer('so_hoc_sinh_tra_loi_dung')->default(0);
            $table->float('ty_le_tra_loi_dung')->default(0); // Percentage
            $table->float('do_kho')->default(0); // Difficulty: 0-1
            $table->float('diem_trung_binh')->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_cau_hoi')->references('id')->on('cau_hois')->onDelete('cascade');
            $table->foreign('id_bai_test')->references('id')->on('bai_tests')->onDelete('cascade');
            $table->index(['id_cau_hoi', 'id_bai_test']);
        });

        // Create answer analytics table
        Schema::create('answer_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dap_an');
            $table->unsignedBigInteger('id_cau_hoi');
            $table->integer('so_hoc_sinh_chon')->default(0);
            $table->float('ty_le_hoc_sinh_chon')->default(0); // Percentage
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_dap_an')->references('id')->on('dap_ans')->onDelete('cascade');
            $table->foreign('id_cau_hoi')->references('id')->on('cau_hois')->onDelete('cascade');
            $table->index(['id_dap_an', 'id_cau_hoi']);
        });

        // Create activity log table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('action'); // created, updated, deleted, submitted, etc
            $table->string('action_type'); // test, question, answer, result
            $table->unsignedBigInteger('related_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->index(['id_user', 'action_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('answer_analytics');
        Schema::dropIfExists('question_analytics');
        Schema::dropIfExists('test_analytics');
    }
};
