<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;

$users = User::select('id', 'name', 'email', 'role', 'is_active')->get();

echo "\n📋 DANH SÁCH CÁC USER TRONG HỆ THỐNG:\n";
echo "=====================================\n";

if ($users->isEmpty()) {
    echo "Không có user nào trong database\n";
} else {
    foreach ($users as $user) {
        echo sprintf(
            "ID: %d | Email: %s | Name: %s | Role: %s | Active: %s\n",
            $user->id,
            $user->email,
            $user->name,
            $user->role,
            $user->is_active ? 'Yes' : 'No'
        );
    }
}

echo "\n🔧 Để tạo giáo viên mới, chạy:\n";
echo "   php artisan tinker\n";
echo "   > App\\Models\\User::create(['name' => 'Teacher', 'email' => 'teacher@test.com', 'password' => 'password', 'role' => 'giao_vien', 'is_active' => true])\n";
