<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Benefit extends Model
{
    public function contracts()
    {
        return $this->belongsToMany(EmployeeContract::class, 'contract_benefits', 'benefit_id', 'employee_contract_id')->withPivot('amount');
    }
}
