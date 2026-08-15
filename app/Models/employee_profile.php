<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employee_profile extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->belongsTo(employees::class,'employee_id');
    }
}
