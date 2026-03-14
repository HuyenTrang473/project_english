<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTestResult extends Model
{
    use HasFactory;

    protected $table = 'student_test_results';

    protected $fillable = [
        'id_hoc_sinh',
        'id_bai_test',
        'lan_thu',
        'thoi_gian_bat_dau',
        'thoi_gian_hoan_thanh',
        'thoi_gian_su_dung',
        'diem_tong',
        'so_cau_dung',
        'so_cau_sai',
        'so_cau_bo_trong',
        'trang_thai',
        'ghi_chu_giao_vien',
    ];

    protected $casts = [
        'lan_thu' => 'integer',
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_hoan_thanh' => 'datetime',
        'thoi_gian_su_dung' => 'integer',
        'diem_tong' => 'float',
        'so_cau_dung' => 'integer',
        'so_cau_sai' => 'integer',
        'so_cau_bo_trong' => 'integer',
    ];

    // Relationships
    public function hocSinh()
    {
        return $this->belongsTo(User::class, 'id_hoc_sinh');
    }

    public function baiTest()
    {
        return $this->belongsTo(BaiTest::class, 'id_bai_test');
    }

    public function studentAnswerDetails()
    {
        return $this->hasMany(StudentAnswerDetail::class, 'id_student_test_result');
    }

    // Scope
    public function scopeNotStarted($query)
    {
        return $query->where('trang_thai', 'not_started');
    }

    public function scopeInProgress($query)
    {
        return $query->where('trang_thai', 'in_progress');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('trang_thai', 'completed');
    }

    public function scopeGraded($query)
    {
        return $query->whereIn('trang_thai', ['pending_review', 'grading']);
    }

    // Helpers
    public function isNotStarted(): bool
    {
        return $this->trang_thai === 'not_started';
    }

    public function isInProgress(): bool
    {
        return $this->trang_thai === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->trang_thai === 'completed';
    }

    public function isGraded(): bool
    {
        return in_array($this->trang_thai, ['pending_review', 'grading']);
    }

    public function calculateScore(): float
    {
        $totalScore = 0;
        $details = $this->studentAnswerDetails;

        foreach ($details as $detail) {
            if ($detail->diem_cau_hoi !== null) {
                $totalScore += $detail->diem_cau_hoi;
            }
        }

        return round($totalScore, 2);
    }
}
