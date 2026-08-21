<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    // relasi ke employees
    public function employees(){
        return $this->belongsTo(Employees::class,'employee_id');
    }
}
