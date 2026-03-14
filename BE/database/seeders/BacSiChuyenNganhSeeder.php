<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BacSiChuyenNganhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bac_si_chuyen_nganhs')->truncate();

        DB::table('bac_si_chuyen_nganhs')->insert([
            [
                'id_bac_si' => 1,
                'id_chuyen_nganh' => 1,
                'thoi_gian_du_kien' => 15,
            ],
            [
                'id_bac_si' => 2,
                'id_chuyen_nganh' => 2,
                'thoi_gian_du_kien' => 30,
            ],
            [
                'id_bac_si' => 3,
                'id_chuyen_nganh' => 3,
                'thoi_gian_du_kien' => 45,
            ],
            [
                'id_bac_si' => 4,
                'id_chuyen_nganh' => 4,
                'thoi_gian_du_kien' => 60,
            ],
            [
                'id_bac_si' => 5,
                'id_chuyen_nganh' => 5,
                'thoi_gian_du_kien' => 75,
            ],
            [
                'id_bac_si' => 6,
                'id_chuyen_nganh' => 6,
                'thoi_gian_du_kien' => 90,
            ],
            [
                'id_bac_si' => 7,
                'id_chuyen_nganh' => 7,
                'thoi_gian_du_kien' => 105,
            ],
            [
                'id_bac_si' => 8,
                'id_chuyen_nganh' => 8,
                'thoi_gian_du_kien' => 120,
            ],
            [
                'id_bac_si' => 9,
                'id_chuyen_nganh' => 5,
                'thoi_gian_du_kien' => 135,
            ],
            [
                'id_bac_si' => 10,
                'id_chuyen_nganh' => 9,
                'thoi_gian_du_kien' => 150,
            ],
        ]);
    }
}
