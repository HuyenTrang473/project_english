<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->truncate();
        DB::table('admins')->insert([
            [
                'id' => 1,
                'ho_ten' => 'Nguyễn Quốc Long',
                'email' => 'quoclongdng@gmail.com',
                'mat_khau' => '123456',
                'so_dien_thoai' => '0987654321',
                'tinh_trang' => 1,

            ],
            [
                'id' => 2,
                'ho_ten' => 'Nguyễn Quốc Long',
                'email' => 'dzfullstack@gmail.com',
                'mat_khau' => '123456',
                'so_dien_thoai' => '1234567890',
                'tinh_trang' => 1,

            ],
        ]);
    }
}
