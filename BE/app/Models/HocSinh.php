<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class HocSinh extends User
{
    protected static function booted(): void
    {
        static::addGlobalScope('role_hoc_sinh', function (Builder $builder) {
            $builder->where('role', 'hoc_sinh');
        });
    }
}
