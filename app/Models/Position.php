<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Position extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->hasMany(Employees::class,'position_id');
    }

    // relasi ke jobdesk
    public function jobdesk(){
        return $this->hasMany(PositionJobdesk::class,'position_id');
    }
}
