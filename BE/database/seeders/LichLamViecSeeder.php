<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LichLamViecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lich_lam_viecs')->truncate();

        DB::table('lich_lam_viecs')->insert([
            [
                'id_bac_si' => 1,
                'ngay_lam_viec' => '2025-07-01',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 1,
                'ngay_lam_viec' => '2025-07-01',
                'thoi_gian_bat_dau' => '13:00',
                'thoi_gian_ket_thuc' => '17:00',
            ],
            [
                'id_bac_si' => 2,
                'ngay_lam_viec' => '2025-07-02',
                'thoi_gian_bat_dau' => '08:30',
                'thoi_gian_ket_thuc' => '12:30',
            ],
            [
                'id_bac_si' => 2,
                'ngay_lam_viec' => '2025-07-02',
                'thoi_gian_bat_dau' => '14:00',
                'thoi_gian_ket_thuc' => '18:00',
            ],
            [
                'id_bac_si' => 3,
                'ngay_lam_viec' => '2025-07-03',
                'thoi_gian_bat_dau' => '09:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 3,
                'ngay_lam_viec' => '2025-07-03',
                'thoi_gian_bat_dau' => '13:30',
                'thoi_gian_ket_thuc' => '17:30',
            ],
            [
                'id_bac_si' => 4,
                'ngay_lam_viec' => '2025-07-04',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 4,
                'ngay_lam_viec' => '2025-07-04',
                'thoi_gian_bat_dau' => '13:00',
                'thoi_gian_ket_thuc' => '17:00',
            ],
            [
                'id_bac_si' => 5,
                'ngay_lam_viec' => '2025-07-05',
                'thoi_gian_bat_dau' => '08:30',
                'thoi_gian_ket_thuc' => '12:30',
            ],
            [
                'id_bac_si' => 5,
                'ngay_lam_viec' => '2025-07-05',
                'thoi_gian_bat_dau' => '14:30',
                'thoi_gian_ket_thuc' => '18:30',
            ],
            [
                'id_bac_si' => 6,
                'ngay_lam_viec' => '2025-07-06',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 6,
                'ngay_lam_viec' => '2025-07-06',
                'thoi_gian_bat_dau' => '13:00',
                'thoi_gian_ket_thuc' => '17:00',
            ],
            [
                'id_bac_si' => 7,
                'ngay_lam_viec' => '2025-07-07',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 7,
                'ngay_lam_viec' => '2025-07-07',
                'thoi_gian_bat_dau' => '13:00',
                'thoi_gian_ket_thuc' => '17:00',
            ],
            [
                'id_bac_si' => 8,
                'ngay_lam_viec' => '2025-07-08',
                'thoi_gian_bat_dau' => '09:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 8,
                'ngay_lam_viec' => '2025-07-08',
                'thoi_gian_bat_dau' => '13:30',
                'thoi_gian_ket_thuc' => '17:30',
            ],
            [
                'id_bac_si' => 9,
                'ngay_lam_viec' => '2025-07-09',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 9,
                'ngay_lam_viec' => '2025-07-09',
                'thoi_gian_bat_dau' => '13:00',
                'thoi_gian_ket_thuc' => '17:00',
            ],
            [
                'id_bac_si' => 10,
                'ngay_lam_viec' => '2025-07-10',
                'thoi_gian_bat_dau' => '08:00',
                'thoi_gian_ket_thuc' => '12:00',
            ],
            [
                'id_bac_si' => 10,
                'ngay_lam_viec' => '2025-07-10',
                'thoi_gian_bat_dau' => '13:30',
                'thoi_gian_ket_thuc' => '17:30',
            ],
        ]);
    }
}
