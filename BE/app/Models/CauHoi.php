<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHoi extends Model
{
    use HasFactory;

    protected $table = 'cau_hois';

    protected $fillable = [
        'id_bai_test',
        'noi_dung',
        'mo_ta_chi_tiet',
        'loai_cau_hoi',
        'ghi_chu',
        'hinh_anh_url',
        'audio_url',
        'audio_file_name',
        'audio_file_size',
        'thu_tu',
        'diem_max',
    ];

    protected $casts = [
        'thu_tu' => 'integer',
        'diem_max' => 'float',
    ];

    // Relationships
    public function baiTest()
    {
        return $this->belongsTo(BaiTest::class, 'id_bai_test');
    }

    public function dapAns()
    {
        return $this->hasMany(DapAn::class, 'id_cau_hoi')->orderBy('thu_tu');
    }

    public function studentAnswerDetails()
    {
        return $this->hasMany(StudentAnswerDetail::class, 'id_cau_hoi');
    }

    public function analytics()
    {
        return $this->hasMany(QuestionAnalytic::class, 'id_cau_hoi');
    }

    // Helpers
    public function getDapAnDung(): ?DapAn
    {
        return $this->dapAns()->where('la_dap_an_dung', true)->first();
    }

    public function isSingleChoice(): bool
    {
        return $this->loai_cau_hoi === 'multiple_choice';
    }

    public function isMultipleChoice(): bool
    {
        return $this->loai_cau_hoi === 'multiple_choice';
    }

    public function isEssay(): bool
    {
        return $this->loai_cau_hoi === 'essay';
    }

    public function isMatching(): bool
    {
        return $this->loai_cau_hoi === 'matching';
    }

    public function isFillBlank(): bool
    {
        return $this->loai_cau_hoi === 'fill_blank';
    }

    public function isTrueFalse(): bool
    {
        return $this->loai_cau_hoi === 'true_false';
    }
}
