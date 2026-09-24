<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeTermination extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
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
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    protected function casts(): array
    {
        return [
            'initiated_at' => 'datetime',
            'effective_date' => 'date',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
