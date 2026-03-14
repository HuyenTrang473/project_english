<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhongKhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phong_khams')->truncate();

        DB::table('phong_khams')->insert([
            [
                'ten_phong_kham' => 'Phòng Khám Đa Khoa Hà Nội',
                'email' => 'pkhanoi@example.com',
                'dia_chi' => '123 Đường Trần Duy Hưng, Cầu Giấy, Hà Nội',
                'so_dien_thoai' => '02435678901',
                'mo_ta' => 'Chuyên khám và điều trị nội tổng hợp.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Sài Gòn',
                'email' => 'pksaigon@example.com',
                'dia_chi' => '456 Nguyễn Trãi, Quận 1, TP.HCM',
                'so_dien_thoai' => '02812345678',
                'mo_ta' => 'Phòng khám uy tín với đội ngũ bác sĩ giỏi.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Đa Khoa Huế',
                'email' => 'pkhue@example.com',
                'dia_chi' => '789 Lê Lợi, TP. Huế',
                'so_dien_thoai' => '02343987654',
                'mo_ta' => 'Dịch vụ nội ngoại khoa chất lượng.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Cần Thơ',
                'email' => 'pkcantho@example.com',
                'dia_chi' => '25 Nguyễn Văn Cừ, Ninh Kiều, Cần Thơ',
                'so_dien_thoai' => '02923765432',
                'mo_ta' => 'Khám chữa bệnh tận tâm.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Đà Nẵng',
                'email' => 'pkdanang@example.com',
                'dia_chi' => '90 Nguyễn Văn Linh, Đà Nẵng',
                'so_dien_thoai' => '02363829383',
                'mo_ta' => 'Thiết bị hiện đại, không gian thoải mái.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Bình Dương',
                'email' => 'pkbinhduong@example.com',
                'dia_chi' => '12 Đại Lộ Bình Dương, Thủ Dầu Một',
                'so_dien_thoai' => '02743827382',
                'mo_ta' => 'Phòng khám chất lượng cao.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Vũng Tàu',
                'email' => 'pkvungtau@example.com',
                'dia_chi' => '15 Lê Hồng Phong, Vũng Tàu',
                'so_dien_thoai' => '02543567890',
                'mo_ta' => 'Khám nhanh, kết quả chính xác.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Hải Phòng',
                'email' => 'pkhaiphong@example.com',
                'dia_chi' => '100 Cầu Đất, Hải Phòng',
                'so_dien_thoai' => '02253847292',
                'mo_ta' => 'Đa dạng chuyên khoa, dịch vụ tốt.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Nha Trang',
                'email' => 'pknhatrang@example.com',
                'dia_chi' => '99 Trần Phú, TP. Nha Trang',
                'so_dien_thoai' => '02583828383',
                'mo_ta' => 'Bác sĩ tận tình, nhiệt huyết.',
                'anh_banner' => 'https://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
            [
                'ten_phong_kham' => 'Phòng Khám Buôn Ma Thuột',
                'email' => 'pkbmt@example.com',
                'dia_chi' => '30 Lý Thường Kiệt, Buôn Ma Thuột',
                'so_dien_thoai' => '02623828282',
                'mo_ta' => 'Khám bệnh ngoài giờ, tiện lợi.',
                'anh_banner' => 'bhttps://nfassetoss.southcn.com/__asset/9fc924a912/16986c753e.jpg',
            ],
        ]);
    }
}
