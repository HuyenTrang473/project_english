<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * DEPRECATED: Audio now stored at question level (CauHoi table)
     * Keeping this for backward compatibility
     */
    public function up(): void
    {
        // Migration không còn sử dụng - đã chuyển sang cau_hois
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
