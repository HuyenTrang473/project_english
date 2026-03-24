<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change loai_cau_hoi from integer to string to support named question types.
     */
    public function up(): void
    {
        // First, convert existing integer values to string equivalents
        DB::statement("ALTER TABLE `cau_hois` MODIFY `loai_cau_hoi` VARCHAR(50) NOT NULL DEFAULT 'multiple_choice'");

        // Map old integer values to new string values
        DB::table('cau_hois')->where('loai_cau_hoi', '1')->update(['loai_cau_hoi' => 'multiple_choice']);
        DB::table('cau_hois')->where('loai_cau_hoi', '2')->update(['loai_cau_hoi' => 'multiple_choice']);
        DB::table('cau_hois')->where('loai_cau_hoi', '3')->update(['loai_cau_hoi' => 'essay']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back string values to integers
        DB::table('cau_hois')->where('loai_cau_hoi', 'multiple_choice')->update(['loai_cau_hoi' => '1']);
        DB::table('cau_hois')->where('loai_cau_hoi', 'essay')->update(['loai_cau_hoi' => '3']);

        DB::statement("ALTER TABLE `cau_hois` MODIFY `loai_cau_hoi` INT NOT NULL DEFAULT 1");
    }
};
