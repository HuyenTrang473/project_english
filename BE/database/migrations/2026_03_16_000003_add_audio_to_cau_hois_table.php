<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột audio_url để lưu đường dẫn file audio (.mp3) cho từng câu hỏi
     * Áp dụng cho câu hỏi listening
     */
    public function up(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            // Đường dẫn file audio cho câu hỏi listening
            $table->string('audio_url')->nullable()->after('hinh_anh_url');
            // Tên file audio gốc (lưu để hiển thị)
            $table->string('audio_file_name')->nullable()->after('audio_url');
            // Dung lượng file audio (bytes)
            $table->bigInteger('audio_file_size')->nullable()->after('audio_file_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hois', function (Blueprint $table) {
            $table->dropColumn(['audio_url', 'audio_file_name', 'audio_file_size']);
        });
    }
};
