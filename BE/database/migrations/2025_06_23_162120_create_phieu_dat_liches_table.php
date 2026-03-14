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
        Schema::create('phieu_dat_lichs', function (Blueprint $table) {
            $table->id();
            $table->integer('id_benh_nhan')->nullable();
            $table->date('ngay_dat_hen')->nullable();
            $table->string('ly_do_kham')->nullable();
            $table->integer('tinh_trang')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_dat_liches');
    }
};
