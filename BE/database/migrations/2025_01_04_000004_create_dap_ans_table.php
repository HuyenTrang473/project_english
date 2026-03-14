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
        Schema::create('dap_ans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cau_hoi')->constrained('cau_hois');
            $table->text('noi_dung');
            $table->boolean('la_dap_an_dung')->default(false);
            $table->integer('thu_tu')->default(0);
            $table->timestamps();

            $table->index(['id_cau_hoi', 'la_dap_an_dung']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_ans');
    }
};
