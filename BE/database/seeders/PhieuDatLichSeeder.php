<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhieuDatLichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phieu_dat_lichs')->truncate();
        DB::table('phieu_dat_lichs')->insert([
            [
                'id_benh_nhan' => 1,
                'ngay_dat_hen' => '2025-07-01',
                'ly_do_kham' => 'Khám tổng quát định kỳ',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 2,
                'ngay_dat_hen' => '2025-07-02',
                'ly_do_kham' => 'Đau đầu kéo dài',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 3,
                'ngay_dat_hen' => '2025-07-03',
                'ly_do_kham' => 'Khám da liễu',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 4,
                'ngay_dat_hen' => '2025-07-04',
                'ly_do_kham' => 'Khám tiêu hóa',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 5,
                'ngay_dat_hen' => '2025-07-05',
                'ly_do_kham' => 'Khám sức khỏe hậu COVID',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 6,
                'ngay_dat_hen' => '2025-07-06',
                'ly_do_kham' => 'Khám huyết áp',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 7,
                'ngay_dat_hen' => '2025-07-07',
                'ly_do_kham' => 'Tái khám tiểu đường',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 8,
                'ngay_dat_hen' => '2025-07-08',
                'ly_do_kham' => 'Khám tim mạch',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 9,
                'ngay_dat_hen' => '2025-07-09',
                'ly_do_kham' => 'Khám hậu sản',
                'tinh_trang' => 1,

            ],
            [
                'id_benh_nhan' => 10,
                'ngay_dat_hen' => '2025-07-10',
                'ly_do_kham' => 'Khám mắt định kỳ',
                'tinh_trang' => 1,

            ],
        ]);
    }
}
