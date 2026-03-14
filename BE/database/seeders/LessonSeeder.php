<?php

namespace Database\Seeders;

use App\Models\BaiTest;
use App\Models\CauHoi;
use App\Models\DapAn;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('role', 'giao_vien')->first();

        // Tạo lessons
        $lesson1 = Lesson::create([
            'id_giao_vien' => $teacher->id,
            'tieu_de' => 'Giới thiệu về Tiếng Anh',
            'mo_ta' => 'Bài học cơ bản về tiếng Anh',
            'noi_dung' => 'Nội dung bài học 1...',
            'trang_thai' => 2, // published
        ]);

        $lesson2 = Lesson::create([
            'id_giao_vien' => $teacher->id,
            'tieu_de' => 'Phát âm tiếng Anh',
            'mo_ta' => 'Học cách phát âm chuẩn',
            'noi_dung' => 'Nội dung bài học 2...',
            'trang_thai' => 2, // published
        ]);

        // Tạo bài test cho lesson 1
        $test1 = BaiTest::create([
            'id_giao_vien' => $teacher->id,
            'id_lesson' => $lesson1->id,
            'ten_bai_test' => 'Test Lesson 1',
            'mo_ta' => 'Kiểm tra bài học 1',
            'thoi_gian_toi_da' => 15, // 15 phút
            'diem_tong_max' => 10.00,
            'trang_thai' => 2, // published
        ]);

        // Tạo câu hỏi cho test
        $question1 = CauHoi::create([
            'id_bai_test' => $test1->id,
            'noi_dung' => 'What is the meaning of "Hello"?',
            'loai_cau_hoi' => 1, // single choice
            'thu_tu' => 1,
            'diem_max' => 1.00,
        ]);

        // Tạo đáp án
        DapAn::create([
            'id_cau_hoi' => $question1->id,
            'noi_dung' => 'Xin chào',
            'la_dap_an_dung' => true,
            'thu_tu' => 1,
        ]);

        DapAn::create([
            'id_cau_hoi' => $question1->id,
            'noi_dung' => 'Tạm biệt',
            'la_dap_an_dung' => false,
            'thu_tu' => 2,
        ]);

        DapAn::create([
            'id_cau_hoi' => $question1->id,
            'noi_dung' => 'Cảm ơn',
            'la_dap_an_dung' => false,
            'thu_tu' => 3,
        ]);

        // Tạo câu hỏi 2
        $question2 = CauHoi::create([
            'id_bai_test' => $test1->id,
            'noi_dung' => 'How do you say "Tạm biệt" in English?',
            'loai_cau_hoi' => 1, // single choice
            'thu_tu' => 2,
            'diem_max' => 1.00,
        ]);

        DapAn::create([
            'id_cau_hoi' => $question2->id,
            'noi_dung' => 'Goodbye',
            'la_dap_an_dung' => true,
            'thu_tu' => 1,
        ]);

        DapAn::create([
            'id_cau_hoi' => $question2->id,
            'noi_dung' => 'Hello',
            'la_dap_an_dung' => false,
            'thu_tu' => 2,
        ]);

        DapAn::create([
            'id_cau_hoi' => $question2->id,
            'noi_dung' => 'Thank you',
            'la_dap_an_dung' => false,
            'thu_tu' => 3,
        ]);
    }
}
