<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class WorkManagementAudit extends Model
{
    public function actor(){return $this->belongsTo(Employees::class, 'actor_id');}
    public function auditable(){return $this->morphTo();}
    protected function casts(): array
    {
        return ['old_values'=>'array','new_values'=>'array'];
    }
}
