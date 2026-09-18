<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class LateDisciplineRule extends Model
{
    protected function casts(): array
    {
        return [
            'threshold' => 'integer',
            'action_amount' => 'decimal:2',
        ];
    }
}
