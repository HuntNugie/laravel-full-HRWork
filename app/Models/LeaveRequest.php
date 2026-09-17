<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class LeaveRequest extends Model
{
    public function employees()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function contract()
    {
        return $this->belongsTo(
            EmployeeContract::class,
            'employee_contract_id'
        );
    }

    public function leaveType()
    {
        return $this->belongsTo(
            LeaveType::class,
            'leave_type_id'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function employeeContract()
    {
        return $this->belongsTo(
            EmployeeContract::class,
            'employee_contract_id'
        );
    }
}
