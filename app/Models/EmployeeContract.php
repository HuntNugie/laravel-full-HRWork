<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeContract extends Model
{
    // relasi ke employees
    public function employees()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    // relasi ke benefit
    public function benefits()
    {
        return $this->belongsToMany(Benefit::class, 'contract_benefits', 'employee_contract_id', 'benefit_id')->withPivot('amount');
    }

    public function contractLeave()
    {
        return $this->hasMany(ContractLeaveEntitlements::class, 'employee_contract_id');
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_contract_id');
    }

    public function resignations()
    {
        return $this->hasMany(EmployeeResignation::class, 'employee_contract_id');
    }

    public function casts()
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date'
        ];
    }
}
