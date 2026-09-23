<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeTermination extends Model
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

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
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

    public function histories()
    {
        return $this->hasMany(EmployeeTerminationHistory::class, 'termination_id');
    }

    public function clearances()
    {
        return $this->hasMany(EmployeeTerminationClearance::class, 'termination_id');
    }

    public function handoverItems()
    {
        return $this->hasMany(EmployeeTerminationHandoverItem::class, 'termination_id');
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
            'initiated_at' => 'datetime',
            'proposed_effective_date' => 'date',
            'approved_effective_date' => 'date',
            'reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
