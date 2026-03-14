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
        Schema::create('cau_hois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bai_test')->constrained('bai_tests');
            $table->text('noi_dung');
            $table->integer('loai_cau_hoi')->default(1); // 1: single choice, 2: multiple choice, 3: essay
            $table->integer('thu_tu')->default(0);
            $table->decimal('diem_max', 5, 2)->default(1.00);
            $table->timestamps();

            $table->index(['id_bai_test', 'thu_tu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hois');
    }
};
