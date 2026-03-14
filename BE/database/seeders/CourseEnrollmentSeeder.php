<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lesson;
use App\Models\CourseEnrollment;
use Illuminate\Database\Seeder;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds - enroll students in lessons
     */
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'hoc_sinh')->get();

        // Get all lessons
        $lessons = Lesson::all();

        // Enroll each student in each lesson
        foreach ($students as $student) {
            foreach ($lessons as $lesson) {
                // Check if not already enrolled
                $existing = CourseEnrollment::where('id_hoc_sinh', $student->id)
                    ->where('id_lesson', $lesson->id)
                    ->first();

                if (!$existing) {
                    CourseEnrollment::create([
                        'id_hoc_sinh' => $student->id,
                        'id_lesson' => $lesson->id,
                        'ngay_dang_ky' => now(),
                    ]);
                }
            }
        }
    }
}
