<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle($request = \Illuminate\Http\Request::capture());

use App\Models\BaiTest;

$tests = BaiTest::with('giaoVien', 'cauHois')->limit(5)->get();
echo "Total tests: " . BaiTest::count() . "\n";
foreach ($tests as $test) {
    echo "Test: {$test->ten_bai_test} | Teacher: {$test->giaoVien->name} | Questions: {$test->cauHois->count()} | Status: {$test->trang_thai}\n";
}
