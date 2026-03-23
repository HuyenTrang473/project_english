<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [
        'id_giao_vien',
        'tieu_de',
        'mo_ta',
        'noi_dung',
        'trang_thai',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
    ];

    // Relationships
    public function giaoVien()
    {
        return $this->belongsTo(User::class, 'id_giao_vien');
    }

    public function baiTests()
    {
        return $this->hasMany(BaiTest::class, 'id_lesson');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'id_lesson');
    }

    public function lessonProgresses()
    {
        return $this->hasMany(LessonProgress::class, 'id_lesson');
    }

    // Scope
    public function scopePublished($query)
    {
        return $query->where('trang_thai', 2);
    }

    public function scopeDraft($query)
    {
        return $query->where('trang_thai', 1);
    }

    // Helpers
    public function isPublished(): bool
    {
        return $this->trang_thai === 2;
    }

    public function isDraft(): bool
    {
        return $this->trang_thai === 1;
    }

    public function publish(): void
    {
        $this->trang_thai = 2;
        $this->save();
    }

    public function unpublish(): void
    {
        $this->trang_thai = 1;
        $this->save();
    }
}
