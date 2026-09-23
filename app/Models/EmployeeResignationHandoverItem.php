<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeResignationHandoverItem extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NOT_APPLICABLE = 'not_applicable';

    public function resignation()
    {
        return $this->belongsTo(EmployeeResignation::class, 'resignation_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function handoverTo()
    {
        return $this->belongsTo(Employees::class, 'handover_to_employee_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
