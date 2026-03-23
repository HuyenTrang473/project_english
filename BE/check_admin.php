<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = \App\Models\User::where('role', 'admin')->first();
if ($admin) {
    echo "=== ADMIN FOUND ===\n";
    echo "Name: " . $admin->name . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Role: " . $admin->role . "\n";
    echo "Is Active: " . ($admin->is_active ? "YES" : "NO") . "\n";
    echo "ID: " . $admin->id . "\n";
} else {
    echo "NO ADMIN FOUND - Creating admin account...\n";
    $admin = \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@tienganhonline.test',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'is_active' => true,
    ]);
    echo "\n=== NEW ADMIN CREATED ===\n";
    echo "Email: " . $admin->email . "\n";
    echo "Password: password\n";
}
