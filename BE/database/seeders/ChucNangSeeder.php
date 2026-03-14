<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_nangs')->truncate();
        DB::table('chuc_nangs')->insert([
            ['ten_chuc_nang' => 'Quản lý người dùng'],
            ['ten_chuc_nang' => 'Xử lý vi phạm'],
            ['ten_chuc_nang' => 'Xem thống kê'],
        ]);
    }
}
