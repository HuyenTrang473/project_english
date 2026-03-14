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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_giao_vien')->constrained('users');
            $table->string('tieu_de');
            $table->text('mo_ta')->nullable();
            $table->text('noi_dung');
            $table->integer('trang_thai')->default(1); // 1: draft, 2: published
            $table->timestamps();

            $table->index(['id_giao_vien', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
