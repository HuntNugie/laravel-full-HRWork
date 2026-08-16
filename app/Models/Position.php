<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->hasMany(Employees::class,'position_id');
    }
}
