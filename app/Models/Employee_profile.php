<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee_profile extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->belongsTo(Employees::class,'employee_id');
    }
}
