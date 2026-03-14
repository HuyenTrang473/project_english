<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerAnalytic extends Model
{
    use HasFactory;

    protected $table = 'answer_analytics';
    public $timestamps = false;

    protected $fillable = [
        'id_dap_an',
        'id_cau_hoi',
        'so_hoc_sinh_chon',
        'ty_le_hoc_sinh_chon',
        'updated_at',
    ];

    protected $casts = [
        'ty_le_hoc_sinh_chon' => 'float',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function answer()
    {
        return $this->belongsTo(DapAn::class, 'id_dap_an');
    }

    public function question()
    {
        return $this->belongsTo(CauHoi::class, 'id_cau_hoi');
    }
}
