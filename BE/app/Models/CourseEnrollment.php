<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $table = 'course_enrollments';

    protected $fillable = [
        'id_hoc_sinh',
        'id_lesson',
        'ngay_dang_ky',
    ];

    protected $casts = [
        'ngay_dang_ky' => 'datetime',
    ];

    // Relationships
    public function hocSinh()
    {
        return $this->belongsTo(User::class, 'id_hoc_sinh');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'id_lesson');
    }
}
