<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class WorkTime extends Model
{
    protected function casts(): array
    {
        return [
            'is_working_day' => 'boolean',
        ];
    }
}
