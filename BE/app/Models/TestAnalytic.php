<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAnalytic extends Model
{
    use HasFactory;

    protected $table = 'test_analytics';
    public $timestamps = false;

    protected $fillable = [
        'id_bai_test',
        'so_hoc_sinh_lam',
        'diem_trung_binh',
        'diem_min',
        'diem_max',
        'ty_le_hoc_sinh_dau',
        'thoi_gian_trung_binh',
        'updated_at',
    ];

    protected $casts = [
        'diem_trung_binh' => 'float',
        'diem_min' => 'float',
        'diem_max' => 'float',
        'ty_le_hoc_sinh_dau' => 'float',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function test()
    {
        return $this->belongsTo(BaiTest::class, 'id_bai_test');
    }
}
