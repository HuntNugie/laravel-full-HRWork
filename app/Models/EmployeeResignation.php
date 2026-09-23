<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeResignation extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function employeeContract()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function exitInterviewer()
    {
        return $this->belongsTo(User::class, 'exit_interview_by');
    }

    public function histories()
    {
        return $this->hasMany(EmployeeResignationHistory::class, 'resignation_id');
    }

    public function clearances()
    {
        return $this->hasMany(EmployeeResignationClearance::class, 'resignation_id');
    }

    public function handoverItems()
    {
        return $this->hasMany(EmployeeResignationHandoverItem::class, 'resignation_id');
    }

    public function isActiveProcess(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
        ], true);
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'proposed_last_working_date' => 'date',
            'approved_last_working_date' => 'date',
            'reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
            'exit_interview_at' => 'datetime',
        ];
    }
}
