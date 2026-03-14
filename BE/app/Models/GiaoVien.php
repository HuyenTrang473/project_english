<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class GiaoVien extends User
{
    protected static function booted(): void
    {
        static::addGlobalScope('role_giao_vien', function (Builder $builder) {
            $builder->where('role', 'giao_vien');
        });
    }
}
