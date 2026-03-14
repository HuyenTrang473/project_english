<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenhNhanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('benh_nhans')->truncate();
        DB::table('benh_nhans')->insert([
            [
                'ho_ten' => 'Nguyễn Văn A',
                'email' => 'pkhanoi@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02435678901',
                'ngay_sinh' => '1990-01-01',
                'gioi_tinh' => 'Nam',
                'dia_chi' => '23 Trần Duy Hưng, Hà Nội',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Trần Thị B',
                'email' => 'pksaigon@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02812345678',
                'ngay_sinh' => '1992-02-02',
                'gioi_tinh' => 'Nữ',
                'dia_chi' => '456 Nguyễn Trãi, TP.HCM',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Lê Văn C',
                'email' => 'pkhue@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02343987654',
                'ngay_sinh' => '1985-03-03',
                'gioi_tinh' => 'Nam',
                'dia_chi' => '789 Lê Lợi, Huế',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Phạm Thị D',
                'email' => 'pkcantho@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02923765432',
                'ngay_sinh' => '1993-04-04',
                'gioi_tinh' => 'Nữ',
                'dia_chi' => '25 Nguyễn Văn Cừ, Cần Thơ',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Ngô Văn E',
                'email' => 'pkdanang@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02363789383',
                'ngay_sinh' => '1980-05-05',
                'gioi_tinh' => 'Nam',
                'dia_chi' => '90 Nguyễn Văn Linh, Đà Nẵng',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Lê Thị F',
                'email' => 'pkbinhduong@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02743827382',
                'ngay_sinh' => '1988-06-06',
                'gioi_tinh' => 'Nữ',
                'dia_chi' => '12 Đại Lộ Bình Dương, Thủ Dầu Một',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Đỗ Văn G',
                'email' => 'pkvungtau@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02543567890',
                'ngay_sinh' => '1995-07-07',
                'gioi_tinh' => 'Nam',
                'dia_chi' => '15 Lê Hồng Phong, Vũng Tàu',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Vũ Thị H',
                'email' => 'pkhaiphong@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02253847292',
                'ngay_sinh' => '1991-08-08',
                'gioi_tinh' => 'Nữ',
                'dia_chi' => '100 Cầu Đất, Hải Phòng',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Bùi Văn I',
                'email' => 'pknhatrang@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02583789234',
                'ngay_sinh' => '1989-09-09',
                'gioi_tinh' => 'Nam',
                'dia_chi' => '99 Trần Phú, Nha Trang',
                'tinh_trang' => 1,

            ],
            [
                'ho_ten' => 'Trịnh Thị K',
                'email' => 'pkbmt@example.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '02623828282',
                'ngay_sinh' => '1996-10-10',
                'gioi_tinh' => 'Nữ',
                'dia_chi' => '30 Lý Thường Kiệt, Buôn Ma Thuột',
                'tinh_trang' => 1,

            ],
        ]);
    }
}
