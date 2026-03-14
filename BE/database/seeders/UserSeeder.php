<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tienganhonline.test',
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Teacher users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Giáo viên $i",
                'email' => "teacher$i@tienganhonline.test",
                'password' => 'password123',
                'role' => 'giao_vien',
                'is_active' => true,
            ]);
        }

        // Student users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Học sinh $i",
                'email' => "student$i@tienganhonline.test",
                'password' => 'password123',
                'role' => 'hoc_sinh',
                'is_active' => true,
            ]);
        }
    }
}
