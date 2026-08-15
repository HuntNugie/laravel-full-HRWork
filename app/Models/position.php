<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class position extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->hasMany(employees::class,'position_id');
    }
}
