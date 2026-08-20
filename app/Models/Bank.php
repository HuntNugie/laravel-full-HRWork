<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Bank extends Model
{
    //relasi ke employee bank account
    public function bankAccount(){
        return $this->hasMany(EmployeeBankAccount::class,'bank_id');
    }
}
