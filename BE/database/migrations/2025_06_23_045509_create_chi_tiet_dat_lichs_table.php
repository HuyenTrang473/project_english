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
        Schema::create('chi_tiet_dat_lichs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_phieu_dat_lich')->unsigned();
            $table->integer('id_bac_si')->unsigned();
            $table->Time('thoi_gian_bat_dau')->nullable();
            $table->Time('thoi_gian_ket_thuc')->nullable();
            $table->integer('so_sao_danh_gia')->nullable();
            $table->string('don_thuoc')->nullable();
            $table->string('chuan_doan')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->Time('thoi_gian_ket_thuc_kham')->nullable();
            $table->string('nhan_xet')->nullable();
            $table->date('ngay_danh_gia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_dat_lichs');
    }
};
