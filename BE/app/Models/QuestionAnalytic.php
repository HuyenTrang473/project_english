<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnalytic extends Model
{
    use HasFactory;

    protected $table = 'question_analytics';
    public $timestamps = false;

    protected $fillable = [
        'id_cau_hoi',
        'id_bai_test',
        'so_hoc_sinh_lam',
        'so_hoc_sinh_tra_loi_dung',
        'ty_le_tra_loi_dung',
        'do_kho',
        'diem_trung_binh',
        'updated_at',
    ];

    protected $casts = [
        'ty_le_tra_loi_dung' => 'float',
        'do_kho' => 'float',
        'diem_trung_binh' => 'float',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function question()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }

    public function test()
    {
        return $this->belongsTo(BaiTest::class, 'id_bai_test');
    }
}
