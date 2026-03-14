<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phan_quyens')->truncate();

        DB::table('phan_quyens')->insert([
            [
                'id_chuc_vu' => 1,
                'id_chuc_nang' => 1,
            ],
            [
                'id_chuc_vu' => 1,
                'id_chuc_nang' => 2,
            ],
            [
                'id_chuc_vu' => 1,
                'id_chuc_nang' => 3,
            ],

            // Quyền cho Nhân viên xử lý (id = 2)
            [
                'id_chuc_vu' => 2,
                'id_chuc_nang' => 2,
            ],
            [
                'id_chuc_vu' => 2,
                'id_chuc_nang' => 3,
            ],
        ]);
    }
}
