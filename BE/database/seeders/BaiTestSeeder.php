<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaiTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = \App\Models\User::where('role', 'giao_vien')->first()
            ?? \App\Models\User::factory()->create(['role' => 'giao_vien', 'name' => 'Teacher Model']);

        $lesson = \App\Models\Lesson::first()
            ?? \App\Models\Lesson::create([
                'id_giao_vien' => $teacher->id,
                'tieu_de' => 'Bài học mẫu cho Test',
                'trang_thai' => 2
            ]);

        $tests = [
            [
                'ten_bai_test' => 'Bài Test Ngữ pháp Tiếng Anh cơ bản',
                'mo_ta' => 'Bài test này đánh giá kiến thức ngữ pháp cơ bản của bạn.',
                'loai_quiz' => 'grammar',
                'thoi_gian_toi_da' => 30, // 30 mins
                'diem_tong_max' => 10,
                'trang_thai' => 2, // Published
                'so_lan_lam_toi_da' => 3,
            ],
            [
                'ten_bai_test' => 'Bài Test Từ vựng TOEIC',
                'mo_ta' => 'Kiểm tra 500 từ vựng thường gặp trong TOEIC.',
                'loai_quiz' => 'vocabulary',
                'thoi_gian_toi_da' => 45,
                'diem_tong_max' => 20,
                'trang_thai' => 2,
                'so_lan_lam_toi_da' => 5,
            ]
        ];

        foreach ($tests as $testData) {
            $testData['id_giao_vien'] = $teacher->id;
            $testData['id_lesson'] = $lesson->id;
            $test = \App\Models\BaiTest::create($testData);

            // Seed questions for each test
            for ($i = 1; $i <= 5; $i++) {
                $cauHoi = \App\Models\CauHoi::create([
                    'id_bai_test' => $test->id,
                    'noi_dung' => "Câu hỏi mẫu số $i cho " . $test->ten_bai_test,
                    'loai_cau_hoi' => 1,
                    'diem_max' => $test->diem_tong_max / 5,
                    'thu_tu' => $i,
                ]);

                // Seed answers
                $dapAnDung = rand(1, 4);
                for ($j = 1; $j <= 4; $j++) {
                    \App\Models\DapAn::create([
                        'id_cau_hoi' => $cauHoi->id,
                        'noi_dung' => "Đáp án lựa chọn $j",
                        'la_dap_an_dung' => ($j === $dapAnDung),
                        'diem_tu_dong' => ($j === $dapAnDung) ? $cauHoi->diem_max : 0,
                        'thu_tu' => $j,
                    ]);
                }
            }
        }
    }
}
