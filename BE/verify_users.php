<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')
    ->select('id', 'name', 'email', 'role', 'is_active')
    ->orderBy('id')
    ->get();

echo "=== SEEDED USERS ===\n";
foreach ($users as $user) {
    echo sprintf(
        "ID: %d | Name: %s | Email: %s | Role: %s | Active: %s\n",
        $user->id,
        $user->name,
        $user->email,
        $user->role,
        $user->is_active ? 'Yes' : 'No'
    );
}

$lessons = DB::table('lessons')->count();
echo "\n=== LESSONS CREATED: $lessons ===\n";

$tests = DB::table('bai_tests')->count();
echo "=== TESTS CREATED: $tests ===\n";
