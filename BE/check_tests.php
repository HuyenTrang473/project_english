<?php

use App\Models\User;
use App\Models\BaiTest;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::capture();

echo "====== Teachers ======\n";
$teachers = User::where('role', 'giao_vien')->get();
foreach ($teachers as $teacher) {
    echo "ID: {$teacher->id} | Name: {$teacher->name} | Email: {$teacher->email}\n";
}

echo "\n====== Tests ======\n";
$tests = BaiTest::with('giaoVien')->get();
echo "Total Tests: " . $tests->count() . "\n";
foreach ($tests->take(5) as $test) {
    echo "- {$test->ten_bai_test} (Teacher: {$test->giaoVien->name}, Status: {$test->trang_thai})\n";
}
