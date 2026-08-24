<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeStatusHistory extends Model
{
    // relasi ke employees
    public function employees()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
}
