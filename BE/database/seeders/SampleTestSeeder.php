<?php

namespace Database\Seeders;

use App\Models\BaiTest;
use App\Models\CauHoi;
use App\Models\DapAn;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy giáo viên đầu tiên
        $teacher = User::where('role', 'giao_vien')->first();

        // Tạo 1 bài học dành riêng cho các bài test mẫu
        $lesson = Lesson::create([
            'id_giao_vien' => $teacher->id,
            'tieu_de' => 'Bài Học Chứa Tests Mẫu',
            'mo_ta' => 'Đây là bài học được tạo tự động để chứa các bài test mẫu theo từng dạng template.',
            'noi_dung' => 'Nội dung bài học tự động.',
            'trang_thai' => 2, // published
        ]);

        $templates = [
            [
                'ten_bai_test' => 'Listening Cơ Bản',
                'loai_quiz' => 'listening',
                'chi_tiet_loai_quiz' => 'Bài nghe đơn giản, 10-15 câu, tập trung vào từ vựng và ngữ pháp cơ bản',
                'mo_ta' => 'Quiz nghe hiểu cơ bản',
                'thoi_gian_toi_da' => 20,
                'diem_tong_max' => 100,
                'so_lan_lam_toi_da' => 3,
            ],
            [
                'ten_bai_test' => 'Listening Nâng Cao',
                'loai_quiz' => 'listening',
                'chi_tiet_loai_quiz' => 'Bài nghe phức tạp, 20-25 câu, đánh giá khả năng nghe hiểu sâu',
                'mo_ta' => 'Quiz nghe hiểu nâng cao',
                'thoi_gian_toi_da' => 45,
                'diem_tong_max' => 200,
                'so_lan_lam_toi_da' => 2,
            ],
            [
                'ten_bai_test' => 'Reading Cơ Bản',
                'loai_quiz' => 'reading',
                'chi_tiet_loai_quiz' => 'Bài đọc đơn giản, 10-15 câu, tập trung vào hiểu ý chính',
                'mo_ta' => 'Quiz đọc hiểu cơ bản',
                'thoi_gian_toi_da' => 25,
                'diem_tong_max' => 100,
                'so_lan_lam_toi_da' => 3,
            ],
            [
                'ten_bai_test' => 'Reading Nâng Cao',
                'loai_quiz' => 'reading',
                'chi_tiet_loai_quiz' => 'Bài đọc phức tạp, 20-25 câu, đánh giá khả năng đọc hiểu chi tiết',
                'mo_ta' => 'Quiz đọc hiểu nâng cao',
                'thoi_gian_toi_da' => 50,
                'diem_tong_max' => 200,
                'so_lan_lam_toi_da' => 2,
            ],
            [
                'ten_bai_test' => 'Writing Cơ Bản',
                'loai_quiz' => 'writing',
                'chi_tiet_loai_quiz' => 'Bài viết đơn giản, 5-8 câu, tập trung vào câu và biểu đạt cơ bản',
                'mo_ta' => 'Quiz viết cơ bản',
                'thoi_gian_toi_da' => 30,
                'diem_tong_max' => 100,
                'so_lan_lam_toi_da' => 3,
            ],
            [
                'ten_bai_test' => 'Writing Nâng Cao',
                'loai_quiz' => 'writing',
                'chi_tiet_loai_quiz' => 'Bài viết phức tạp, 8-12 câu, đánh giá kỹ năng viết chi tiết',
                'mo_ta' => 'Quiz viết nâng cao',
                'thoi_gian_toi_da' => 60,
                'diem_tong_max' => 200,
                'so_lan_lam_toi_da' => 2,
            ],
            [
                'ten_bai_test' => 'Test Hỗn Hợp Chuẩn',
                'loai_quiz' => 'mixed',
                'chi_tiet_loai_quiz' => 'Test năng lực hỗn hợp chuẩn, kết hợp nghe, đọc, viết',
                'mo_ta' => 'Test tổng hợp tất cả kỹ năng',
                'thoi_gian_toi_da' => 90,
                'diem_tong_max' => 300,
                'so_lan_lam_toi_da' => 2,
            ],
            [
                'ten_bai_test' => 'Test Năng Lực Toàn Diện',
                'loai_quiz' => 'mixed',
                'chi_tiet_loai_quiz' => 'Test đánh giá toàn diện, chi tiết tất cả các kỹ năng tiếng Anh',
                'mo_ta' => 'Đánh giá chi tiết tất cả kỹ năng',
                'thoi_gian_toi_da' => 120,
                'diem_tong_max' => 500,
                'so_lan_lam_toi_da' => 1,
            ],
        ];

        foreach ($templates as $template) {
            $test = BaiTest::create([
                'id_giao_vien' => $teacher->id,
                'id_lesson' => $lesson->id,
                'ten_bai_test' => $template['ten_bai_test'],
                'loai_quiz' => $template['loai_quiz'],
                'chi_tiet_loai_quiz' => $template['chi_tiet_loai_quiz'],
                'mo_ta' => $template['mo_ta'],
                'thoi_gian_toi_da' => $template['thoi_gian_toi_da'],
                'diem_tong_max' => $template['diem_tong_max'],
                'so_lan_lam_toi_da' => $template['so_lan_lam_toi_da'],
                'trang_thai' => 2, // published
            ]);

            // Thêm 1 câu hỏi mẫu cho bài test
            $question = CauHoi::create([
                'id_bai_test' => $test->id,
                'noi_dung' => 'Câu hỏi mẫu cho ' . $template['ten_bai_test'],
                'loai_cau_hoi' => 1,
                'thu_tu' => 1,
                'diem_max' => 10.00,
            ]);

            DapAn::create([
                'id_cau_hoi' => $question->id,
                'noi_dung' => 'Đáp án đúng',
                'la_dap_an_dung' => true,
                'thu_tu' => 1,
            ]);

            DapAn::create([
                'id_cau_hoi' => $question->id,
                'noi_dung' => 'Đáp án sai',
                'la_dap_an_dung' => false,
                'thu_tu' => 2,
            ]);
        }
    }
}
