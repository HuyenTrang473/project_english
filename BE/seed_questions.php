<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get first test
$test = DB::table('bai_tests')->first();
$lesson = DB::table('lessons')->first();
$teacher = DB::table('users')->where('role', 'giao_vien')->first();

if (!$test || !$lesson) {
    echo "Error: No test or lesson found\n";
    exit;
}

echo "Adding sample questions to test: " . $test->ten_bai_test . "\n";
echo "Test ID: " . $test->id . "\n\n";

// Add 3 sample questions
// loai_cau_hoi: 1 = single choice, 2 = multiple choice, 3 = essay
$questions = [
    [
        'noi_dung' => 'What is the past tense of "go"?',
        'loai_cau_hoi' => 2, // multiple choice
        'answers' => [
            ['noi_dung' => 'went', 'la_dap_an_dung' => true],
            ['noi_dung' => 'goes', 'la_dap_an_dung' => false],
            ['noi_dung' => 'going', 'la_dap_an_dung' => false],
            ['noi_dung' => 'gone', 'la_dap_an_dung' => false],
        ]
    ],
    [
        'noi_dung' => 'Choose the correct sentence:',
        'loai_cau_hoi' => 2, // multiple choice
        'answers' => [
            ['noi_dung' => 'She go to school every day', 'la_dap_an_dung' => false],
            ['noi_dung' => 'She goes to school every day', 'la_dap_an_dung' => true],
            ['noi_dung' => 'She going to school every day', 'la_dap_an_dung' => false],
            ['noi_dung' => 'She went to school every day', 'la_dap_an_dung' => false],
        ]
    ],
    [
        'noi_dung' => 'What is the plural of "child"?',
        'loai_cau_hoi' => 1, // single choice
        'answers' => [
            ['noi_dung' => 'childs', 'la_dap_an_dung' => false],
            ['noi_dung' => 'children', 'la_dap_an_dung' => true],
            ['noi_dung' => 'childes', 'la_dap_an_dung' => false],
            ['noi_dung' => 'childies', 'la_dap_an_dung' => false],
        ]
    ]
];

$questionCount = 0;
foreach ($questions as $qData) {
    $questionId = DB::table('cau_hois')->insertGetId([
        'id_bai_test' => $test->id,
        'noi_dung' => $qData['noi_dung'],
        'loai_cau_hoi' => $qData['loai_cau_hoi'],
        'thu_tu' => ++$questionCount,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $answerCount = 0;
    foreach ($qData['answers'] as $aData) {
        DB::table('dap_ans')->insert([
            'id_cau_hoi' => $questionId,
            'noi_dung' => $aData['noi_dung'],
            'la_dap_an_dung' => $aData['la_dap_an_dung'],
            'thu_tu' => ++$answerCount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    echo "✓ Question $questionCount: " . substr($qData['noi_dung'], 0, 50) . "...\n";
    echo "  Added " . count($qData['answers']) . " answers\n";
}

// Update test with question count and settings
$totalQuestions = DB::table('cau_hois')->where('id_bai_test', $test->id)->count();
DB::table('bai_tests')
    ->where('id', $test->id)
    ->update([
        'diem_tong_max' => $totalQuestions,
        'so_lan_lam_toi_da' => 3,
        'co_xao_tron_cau_hoi' => false,
        'co_xao_tron_dap_an' => false,
        'hien_thi_ket_qua_ngay_lap' => true,
        'hien_thi_dap_an_dung' => true,
        'cho_xem_lai_test' => true,
    ]);

echo "\n✓ Test updated: $totalQuestions questions, max score: $totalQuestions\n";
echo "\nTest is ready for students to take!\n";
