<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswerDetail extends Model
{
    use HasFactory;

    protected $table = 'student_answer_details';

    protected $fillable = [
        'id_student_test_result',
        'id_cau_hoi',
        'id_dap_an',
        'cau_tra_loi_tu_do',
        'diem_cau_hoi',
        'la_dung',
    ];

    protected $casts = [
        'diem_cau_hoi' => 'float',
        'la_dung' => 'boolean',
    ];

    // Relationships
    public function studentTestResult()
    {
        return $this->belongsTo(StudentTestResult::class, 'id_student_test_result');
    }

    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }

    public function dapAn()
    {
        return $this->belongsTo(DapAn::class, 'id_dap_an');
    }

    // Scope
    public function scopeCorrect($query)
    {
        return $query->where('la_dung', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('la_dung', false);
    }

    // Helpers
    public function isCorrect(): bool
    {
        return $this->la_dung === true;
    }

    public function isIncorrect(): bool
    {
        return $this->la_dung === false;
    }
}
