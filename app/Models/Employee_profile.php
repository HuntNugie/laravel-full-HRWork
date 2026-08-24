<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Employee_profile extends Model
{
    // relasi ke employees
    public function employees()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    // relasi ke employee bank account
    public function bankAccount()
    {
        return $this->hasOne(EmployeeBankAccount::class, 'employee_profile_id');
    }

    // relasi ke address
    public function addressProfile()
    {
        return $this->hasOne(EmployeeProfileAddress::class, 'employee_profile_id');
    }
}
