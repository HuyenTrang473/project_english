<?php

namespace Database\Seeders;

use App\Models\BaiTest;
use App\Models\CauHoi;
use App\Models\DapAn;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnglishTestSampleSeeder extends Seeder
{
    /**
     * Seed realistic English test data.
     */
    public function run(): void
    {
        $teacher = User::where('role', 'giao_vien')->first();

        if (!$teacher) {
            $teacher = User::create([
                'name' => 'Giáo viên Seed Mẫu',
                'email' => 'teacher.seed.sample@tienganhonline.test',
                'password' => 'password123',
                'role' => 'giao_vien',
                'is_active' => true,
            ]);
        }

        $lesson = Lesson::firstOrCreate(
            ['tieu_de' => 'English Placement & Practice'],
            [
                'id_giao_vien' => $teacher->id,
                'mo_ta' => 'Bài học dùng để chứa các bài test tiếng Anh mẫu cho luyện tập và kiểm thử.',
                'noi_dung' => 'Seeded lesson content for test data.',
                'trang_thai' => 2,
            ]
        );

        $tests = $this->getTestDataset();

        foreach ($tests as $testData) {
            $test = BaiTest::updateOrCreate(
                [
                    'id_lesson' => $lesson->id,
                    'ten_bai_test' => $testData['ten_bai_test'],
                ],
                [
                    'id_giao_vien' => $teacher->id,
                    'loai_quiz' => $testData['loai_quiz'],
                    'chi_tiet_loai_quiz' => $testData['chi_tiet_loai_quiz'],
                    'mo_ta' => $testData['mo_ta'],
                    'thoi_gian_toi_da' => $testData['thoi_gian_toi_da'],
                    'diem_tong_max' => $testData['diem_tong_max'],
                    'diem_dat' => $testData['diem_dat'],
                    'trang_thai' => 2,
                    'so_lan_lam_toi_da' => 3,
                    'co_xao_tron_cau_hoi' => false,
                    'co_xao_tron_dap_an' => true,
                    'hien_thi_ket_qua_ngay_lap' => true,
                    'hien_thi_dap_an_dung' => true,
                    'cho_xem_lai_test' => true,
                ]
            );

            $test->cauHois()->delete();

            foreach ($testData['questions'] as $index => $questionData) {
                $question = CauHoi::create([
                    'id_bai_test' => $test->id,
                    'noi_dung' => $questionData['noi_dung'],
                    'mo_ta_chi_tiet' => $questionData['mo_ta_chi_tiet'] ?? null,
                    'loai_cau_hoi' => $questionData['loai_cau_hoi'] ?? 'multiple_choice',
                    'audio_url' => $questionData['audio_url'] ?? null,
                    'audio_file_name' => $questionData['audio_file_name'] ?? null,
                    'audio_file_size' => $questionData['audio_file_size'] ?? null,
                    'thu_tu' => $index + 1,
                    'diem_max' => $questionData['diem_max'],
                ]);

                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    DapAn::create([
                        'id_cau_hoi' => $question->id,
                        'noi_dung' => $answerData['noi_dung'],
                        'la_dap_an_dung' => $answerData['la_dap_an_dung'],
                        'diem_tu_dong' => $answerData['la_dap_an_dung'] ? $question->diem_max : 0,
                        'thu_tu' => $answerIndex + 1,
                    ]);
                }
            }
        }
    }

    private function getTestDataset(): array
    {
        return [
            [
                'ten_bai_test' => 'English Grammar Foundation Test',
                'loai_quiz' => 'grammar',
                'chi_tiet_loai_quiz' => 'Kiểm tra thì, mạo từ và cấu trúc ngữ pháp cơ bản.',
                'mo_ta' => 'Dành cho trình độ A2-B1, gồm các câu trắc nghiệm ngữ pháp thông dụng.',
                'thoi_gian_toi_da' => 20,
                'diem_tong_max' => 20,
                'diem_dat' => 12,
                'questions' => [
                    [
                        'noi_dung' => 'Choose the correct sentence.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 4,
                        'answers' => [
                            ['noi_dung' => 'She go to school every day.', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'She goes to school every day.', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'She going to school every day.', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'She gone to school every day.', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'I ___ to Hanoi last month.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 4,
                        'answers' => [
                            ['noi_dung' => 'go', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'went', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'gone', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'going', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'We need ___ umbrella. It is raining.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 4,
                        'answers' => [
                            ['noi_dung' => 'a', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'an', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'the', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'no article', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'If I ___ enough money, I will buy a laptop.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 4,
                        'answers' => [
                            ['noi_dung' => 'have', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'had', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'having', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'has', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'My brother is interested ___ science fiction.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 4,
                        'answers' => [
                            ['noi_dung' => 'on', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'at', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'in', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'for', 'la_dap_an_dung' => false],
                        ],
                    ],
                ],
            ],
            [
                'ten_bai_test' => 'English Vocabulary In Context',
                'loai_quiz' => 'vocabulary',
                'chi_tiet_loai_quiz' => 'Kiểm tra từ vựng theo ngữ cảnh đời sống và học tập.',
                'mo_ta' => 'Bộ câu hỏi về collocation, synonym và từ vựng thường gặp.',
                'thoi_gian_toi_da' => 18,
                'diem_tong_max' => 15,
                'diem_dat' => 9,
                'questions' => [
                    [
                        'noi_dung' => 'The meeting was ___ because the manager was sick.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 3,
                        'answers' => [
                            ['noi_dung' => 'postponed', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'improved', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'invented', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'adopted', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Choose the word closest in meaning to "essential".',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 3,
                        'answers' => [
                            ['noi_dung' => 'optional', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'necessary', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'difficult', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'ordinary', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'He made a quick ___ before signing the contract.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 3,
                        'answers' => [
                            ['noi_dung' => 'decision', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'chance', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'advice', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'doubt', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Her presentation was clear and ___.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 3,
                        'answers' => [
                            ['noi_dung' => 'confusing', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'informative', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'careless', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'useless', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'We should ___ attention to pronunciation.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 3,
                        'answers' => [
                            ['noi_dung' => 'do', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'take', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'pay', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'make', 'la_dap_an_dung' => false],
                        ],
                    ],
                ],
            ],
            [
                'ten_bai_test' => 'English Reading Comprehension Mini Test',
                'loai_quiz' => 'reading',
                'chi_tiet_loai_quiz' => 'Đọc đoạn văn ngắn và trả lời câu hỏi.',
                'mo_ta' => 'Kiểm tra kỹ năng đọc hiểu thông tin chi tiết và ý chính.',
                'thoi_gian_toi_da' => 25,
                'diem_tong_max' => 20,
                'diem_dat' => 12,
                'questions' => [
                    [
                        'noi_dung' => 'Read the paragraph: "Lan studies English every evening for one hour. She listens to podcasts and writes new words in her notebook." What does Lan do every evening?',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'She studies English for one hour.', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'She watches movies all night.', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'She plays games with friends.', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'She practices speaking at school only.', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Based on the paragraph, which activity helps Lan improve listening?',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'Writing essays', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Listening to podcasts', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'Reading comics', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Doing math homework', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'What does she write in her notebook?',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'Song lyrics', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Grammar rules only', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'New words', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'Exam scores', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Which title is the best for this paragraph?',
                        'loai_cau_hoi' => 'multiple_choice',
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'Lan\'s Travel Plan', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Lan\'s English Study Routine', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'Lan\'s Favorite Sports', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Lan\'s Family Dinner', 'la_dap_an_dung' => false],
                        ],
                    ],
                ],
            ],
            [
                'ten_bai_test' => 'English Listening + Writing Practice',
                'loai_quiz' => 'mixed',
                'chi_tiet_loai_quiz' => 'Kết hợp câu hỏi listening và writing tự luận ngắn.',
                'mo_ta' => 'Dùng để test cả luồng chấm tự động và câu hỏi essay.',
                'thoi_gian_toi_da' => 30,
                'diem_tong_max' => 25,
                'diem_dat' => 15,
                'questions' => [
                    [
                        'noi_dung' => 'Listen to the sentence and choose what the speaker will do tomorrow.',
                        'mo_ta_chi_tiet' => 'Audio demo path for listening workflow.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'audio_url' => 'storage/audio/questions/demo-listening-1.mp3',
                        'audio_file_name' => 'demo-listening-1.mp3',
                        'audio_file_size' => 128000,
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'Go to the dentist', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Meet a client at 9 AM', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'Visit his grandparents', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Stay home all day', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Which word did you hear as the final answer?',
                        'mo_ta_chi_tiet' => 'Audio demo path for listening workflow.',
                        'loai_cau_hoi' => 'multiple_choice',
                        'audio_url' => 'storage/audio/questions/demo-listening-2.mp3',
                        'audio_file_name' => 'demo-listening-2.mp3',
                        'audio_file_size' => 98000,
                        'diem_max' => 5,
                        'answers' => [
                            ['noi_dung' => 'Schedule', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Project', 'la_dap_an_dung' => false],
                            ['noi_dung' => 'Deadline', 'la_dap_an_dung' => true],
                            ['noi_dung' => 'Budget', 'la_dap_an_dung' => false],
                        ],
                    ],
                    [
                        'noi_dung' => 'Write 3-4 sentences about your English learning goals this year.',
                        'loai_cau_hoi' => 'essay',
                        'diem_max' => 15,
                        'answers' => [
                            [
                                'noi_dung' => 'Essay question - teacher will grade manually.',
                                'la_dap_an_dung' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
