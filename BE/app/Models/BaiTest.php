<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiTest extends Model
{
    use HasFactory;

    protected $table = 'bai_tests';

    protected $fillable = [
        'id_giao_vien',
        'id_lesson',
        'ten_bai_test',
        'mo_ta',
        'thoi_gian_toi_da',
        'diem_tong_max',
        'trang_thai',
        'so_lan_lam_toi_da',
        'co_xao_tron_cau_hoi',
        'co_xao_tron_dap_an',
        'hien_thi_ket_qua_ngay_lap',
        'hien_thi_dap_an_dung',
        'cho_xem_lai_test',
        'ngay_bat_dau',
        'ngay_ket_thuc',
    ];

    protected $casts = [
        'thoi_gian_toi_da' => 'integer',
        'diem_tong_max' => 'float',
        'so_lan_lam_toi_da' => 'integer',
        'co_xao_tron_cau_hoi' => 'boolean',
        'co_xao_tron_dap_an' => 'boolean',
        'hien_thi_ket_qua_ngay_lap' => 'boolean',
        'hien_thi_dap_an_dung' => 'boolean',
        'cho_xem_lai_test' => 'boolean',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];

    // Relationships
    public function giaoVien()
    {
        return $this->belongsTo(User::class, 'id_giao_vien');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'id_lesson');
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_bai_test');
    }

    public function studentTestResults()
    {
        return $this->hasMany(StudentTestResult::class, 'id_bai_test');
    }

    public function analytics()
    {
        return $this->hasOne(TestAnalytic::class, 'id_bai_test');
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

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('id_giao_vien', $teacherId);
    }

    // Helpers
    public function isPublished(): bool
    {
        return $this->trang_thai == 2;
    }

    public function isDraft(): bool
    {
        return $this->trang_thai == 1;
    }

    public function getTotalQuestions(): int
    {
        return $this->cauHois()->count();
    }
}
