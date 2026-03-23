<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('role', 'giao_vien')->first();
if ($user) {
    echo "=== GIÁO VIÊN FOUND ===\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Is Active: " . ($user->is_active ? "YES" : "NO") . "\n";
    echo "ID: " . $user->id . "\n";
} else {
    echo "NO TEACHER FOUND\n";
}

$students = \App\Models\User::where('role', 'hoc_sinh')->count();
echo "\nTotal Students: " . $students . "\n";
