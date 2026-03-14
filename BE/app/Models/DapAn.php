<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DapAn extends Model
{
    use HasFactory;

    protected $table = 'dap_ans';
    public $timestamps = false;

    protected $fillable = [
        'id_cau_hoi',
        'noi_dung',
        'la_dap_an_dung',
        'diem_tu_dong',
        'hinh_anh_url',
        'mo_ta_chi_tiet',
        'thu_tu',
    ];

    protected $casts = [
        'la_dap_an_dung' => 'boolean',
        'diem_tu_dong' => 'float',
        'thu_tu' => 'integer',
    ];

    // Relationships
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }

    public function studentAnswerDetails()
    {
        return $this->hasMany(StudentAnswerDetail::class, 'id_dap_an');
    }

    // Scope
    public function scopeCorrect($query)
    {
        return $query->where('la_dap_an_dung', true);
    }

    // Helpers
    public function isCorrect(): bool
    {
        return $this->la_dap_an_dung === true;
    }
}
