<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progresses';

    protected $fillable = [
        'id_hoc_sinh',
        'id_lesson',
        'tien_do_phan_tram',
        'thoi_gian_bat_dau',
        'thoi_gian_hoan_thanh',
    ];

    protected $casts = [
        'tien_do_phan_tram' => 'integer',
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_hoan_thanh' => 'datetime',
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

    // Scope
    public function scopeCompleted($query)
    {
        return $query->where('tien_do_phan_tram', 100);
    }

    public function scopeInProgress($query)
    {
        return $query->whereBetween('tien_do_phan_tram', [1, 99]);
    }

    public function scopeNotStarted($query)
    {
        return $query->where('tien_do_phan_tram', 0);
    }

    // Helpers
    public function isCompleted(): bool
    {
        return $this->tien_do_phan_tram == 100;
    }

    public function isInProgress(): bool
    {
        return $this->tien_do_phan_tram > 0 && $this->tien_do_phan_tram < 100;
    }

    public function isNotStarted(): bool
    {
        return $this->tien_do_phan_tram == 0;
    }
}
