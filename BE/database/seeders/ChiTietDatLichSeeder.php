<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietDatLichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chi_tiet_dat_lichs')->truncate();
        DB::table('chi_tiet_dat_lichs')->insert([
            [
                'id_phieu_dat_lich' => 1,
                'id_bac_si' => 1,
                'thoi_gian_bat_dau' => '2025-07-01 08:00:00',
                'thoi_gian_ket_thuc' => '2025-07-01 08:30:00',
                'so_sao_danh_gia' => 5,
                'don_thuoc' => 'Paracetamol 500mg',
                'chuan_doan' => 'Sốt nhẹ',
                'ghi_chu' => 'Nghỉ ngơi và uống nước',
                'thoi_gian_ket_thuc_kham' => '2025-07-01 08:30:00',
                'nhan_xet' => 'Phục vụ tốt',
                'ngay_danh_gia' => '2025-07-02',

            ],
            [
                'id_phieu_dat_lich' => 2,
                'id_bac_si' => 2,
                'thoi_gian_bat_dau' => '2025-07-02 09:00:00',
                'thoi_gian_ket_thuc' => '2025-07-02 09:40:00',
                'so_sao_danh_gia' => 4,
                'don_thuoc' => 'Panadol Extra',
                'chuan_doan' => 'Đau đầu',
                'ghi_chu' => 'Tránh căng thẳng',
                'thoi_gian_ket_thuc_kham' => '2025-07-02 09:40:00',
                'nhan_xet' => 'Bác sĩ tận tình',
                'ngay_danh_gia' => '2025-07-03',

            ],
            [
                'id_phieu_dat_lich' => 3,
                'id_bac_si' => 3,
                'thoi_gian_bat_dau' => '2025-07-03 10:00:00',
                'thoi_gian_ket_thuc' => '2025-07-03 10:30:00',
                'so_sao_danh_gia' => 5,
                'don_thuoc' => 'Thuốc mỡ da liễu',
                'chuan_doan' => 'Viêm da',
                'ghi_chu' => '',
                'thoi_gian_ket_thuc_kham' => '2025-07-03 10:30:00',
                'nhan_xet' => 'Rất chuyên nghiệp',
                'ngay_danh_gia' => '2025-07-04',

            ],
            [
                'id_phieu_dat_lich' => 4,
                'id_bac_si' => 4,
                'thoi_gian_bat_dau' => '2025-07-04 07:30:00',
                'thoi_gian_ket_thuc' => '2025-07-04 08:00:00',
                'so_sao_danh_gia' => 3,
                'don_thuoc' => 'Men tiêu hóa',
                'chuan_doan' => 'Rối loạn tiêu hóa',
                'ghi_chu' => 'Hạn chế dầu mỡ',
                'thoi_gian_ket_thuc_kham' => '2025-07-04 08:00:00',
                'nhan_xet' => 'Ổn',
                'ngay_danh_gia' => '2025-07-05',

            ],
            [
                'id_phieu_dat_lich' => 5,
                'id_bac_si' => 5,
                'thoi_gian_bat_dau' => '2025-07-05 13:00:00',
                'thoi_gian_ket_thuc' => '2025-07-05 13:30:00',
                'so_sao_danh_gia' => 4,
                'don_thuoc' => 'Vitamin tổng hợp',
                'chuan_doan' => 'Mệt mỏi sau covid',
                'ghi_chu' => 'Nghỉ ngơi nhiều hơn',
                'thoi_gian_ket_thuc_kham' => '2025-07-05 13:30:00',
                'nhan_xet' => 'Hài lòng',
                'ngay_danh_gia' => '2025-07-06',

            ],
            [
                'id_phieu_dat_lich' => 6,
                'id_bac_si' => 6,
                'thoi_gian_bat_dau' => '2025-07-06 15:00:00',
                'thoi_gian_ket_thuc' => '2025-07-06 15:30:00',
                'so_sao_danh_gia' => 5,
                'don_thuoc' => 'Thuốc hạ áp',
                'chuan_doan' => 'Cao huyết áp',
                'ghi_chu' => 'Theo dõi huyết áp hàng ngày',
                'thoi_gian_ket_thuc_kham' => '2025-07-06 15:30:00',
                'nhan_xet' => 'Bác sĩ hướng dẫn kỹ',
                'ngay_danh_gia' => '2025-07-07',

            ],
            [
                'id_phieu_dat_lich' => 7,
                'id_bac_si' => 7,
                'thoi_gian_bat_dau' => '2025-07-07 08:30:00',
                'thoi_gian_ket_thuc' => '2025-07-07 09:00:00',
                'so_sao_danh_gia' => 4,
                'don_thuoc' => 'Thuốc tiểu đường',
                'chuan_doan' => 'Tiểu đường type 2',
                'ghi_chu' => 'Kiểm soát chế độ ăn',
                'thoi_gian_ket_thuc_kham' => '2025-07-07 09:00:00',
                'nhan_xet' => 'Tư vấn đầy đủ',
                'ngay_danh_gia' => '2025-07-08',

            ],
            [
                'id_phieu_dat_lich' => 8,
                'id_bac_si' => 8,
                'thoi_gian_bat_dau' => '2025-07-08 10:00:00',
                'thoi_gian_ket_thuc' => '2025-07-08 10:30:00',
                'so_sao_danh_gia' => 5,
                'don_thuoc' => 'Aspirin 81mg',
                'chuan_doan' => 'Đau thắt ngực nhẹ',
                'ghi_chu' => 'Theo dõi ECG',
                'thoi_gian_ket_thuc_kham' => '2025-07-08 10:30:00',
                'nhan_xet' => 'Chuyên môn tốt',
                'ngay_danh_gia' => '2025-07-09',

            ],
            [
                'id_phieu_dat_lich' => 9,
                'id_bac_si' => 9,
                'thoi_gian_bat_dau' => '2025-07-09 11:00:00',
                'thoi_gian_ket_thuc' => '2025-07-09 11:20:00',
                'so_sao_danh_gia' => 3,
                'don_thuoc' => 'Viên sắt',
                'chuan_doan' => 'Thiếu máu sau sinh',
                'ghi_chu' => 'Tái khám sau 2 tuần',
                'thoi_gian_ket_thuc_kham' => '2025-07-09 11:20:00',
                'nhan_xet' => 'Cần cải thiện thời gian chờ',
                'ngay_danh_gia' => '2025-07-10',

            ],
            [
                'id_phieu_dat_lich' => 10,
                'id_bac_si' => 10,
                'thoi_gian_bat_dau' => '2025-07-10 14:00:00',
                'thoi_gian_ket_thuc' => '2025-07-10 14:25:00',
                'so_sao_danh_gia' => 4,
                'don_thuoc' => 'Vitamin A + thuốc nhỏ mắt',
                'chuan_doan' => 'Khô mắt, mỏi mắt',
                'ghi_chu' => 'Hạn chế dùng máy tính',
                'thoi_gian_ket_thuc_kham' => '2025-07-10 14:25:00',
                'nhan_xet' => 'Phòng khám sạch sẽ',
                'ngay_danh_gia' => '2025-07-11',

            ],

        ]);
    }
}
