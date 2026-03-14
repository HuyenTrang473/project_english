<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'id_giao_vien');
    }

    public function baiTests()
    {
        return $this->hasMany(BaiTest::class, 'id_giao_vien');
    }

    public function studentTestResults()
    {
        return $this->hasMany(StudentTestResult::class, 'id_hoc_sinh');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'id_hoc_sinh');
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, 'id_hoc_sinh');
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'giao_vien');
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'hoc_sinh');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'giao_vien';
    }

    public function isStudent(): bool
    {
        return $this->role === 'hoc_sinh';
    }
}
