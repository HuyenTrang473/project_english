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
        Schema::create('bai_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('users');
            $table->foreignId('id_lesson')->constrained('lessons');
            $table->string('ten_bai_test');
            $table->text('mo_ta')->nullable();
            $table->integer('thoi_gian_toi_da')->default(60); // phút
            $table->decimal('diem_tong_max', 5, 2)->default(100.00);
            $table->integer('trang_thai')->default(1); // 1: draft, 2: published
            $table->integer('so_lan_lam_toi_da')->default(1); // số lần làm tối đa
            $table->boolean('co_xao_tron_cau_hoi')->default(false); // trộn câu hỏi
            $table->boolean('co_xao_tron_dap_an')->default(false); // trộn đáp án
            $table->boolean('hien_thi_ket_qua_ngay_lap')->default(true); // hiển thị kết quả ngay
            $table->boolean('hien_thi_dap_an_dung')->default(true); // hiển thị đáp án đúng
            $table->boolean('cho_xem_lai_test')->default(true); // cho xem lại test
            $table->decimal('diem_dat', 5, 2)->nullable(); // điểm đạt
            $table->timestamp('ngay_bat_dau')->nullable(); // ngày bắt đầu
            $table->timestamp('ngay_ket_thuc')->nullable(); // ngày kết thúc
            $table->timestamps();

            $table->index(['id_giao_vien', 'id_lesson', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_tests');
    }
};
