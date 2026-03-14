<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuyenNganhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuyen_nganhs')->truncate();
        DB::table('chuyen_nganhs')->insert([
            ['ten_chuyen_nganh' => 'Tim mạch'],
            ['ten_chuyen_nganh' => 'Da liễu'],
            ['ten_chuyen_nganh' => 'Ngoại thần kinh'],
            ['ten_chuyen_nganh' => 'Nhi khoa'],
            ['ten_chuyen_nganh' => 'Chấn thương chỉnh hình'],
            ['ten_chuyen_nganh' => 'Nội tiết'],
            ['ten_chuyen_nganh' => 'Tiêu hóa'],
            ['ten_chuyen_nganh' => 'Tâm lý học'],
            ['ten_chuyen_nganh' => 'Phẫu thuật nội soi'],
            ['ten_chuyen_nganh' => 'Sản phụ khoa'],
        ]);
    }
}
