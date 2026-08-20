<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeBankAccount extends Model
{
    //relasi ke employee
    public function employee_profile()
    {
        return $this->belongsTo(Employee_profile::class, 'employee_profile_id');
    }

    // relasi ke bank
    public function bank(){
        return $this->belongsTo(Bank::class,'bank_id');
    }
}
