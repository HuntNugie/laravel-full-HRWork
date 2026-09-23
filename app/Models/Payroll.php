<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class Payroll extends Model
{

    public function items(): HasMany
    {
        return $this->hasMany(
            PayrollItem::class,
            'payroll_id'
        );
    }
    protected function casts(): array
    {
        return [
            'salary_daily' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'deduction_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'processed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }


    public function period(): BelongsTo
    {
        return $this->belongsTo(
            PayrollPeriod::class,
            'payroll_period_id'
        );
    }


    public function employees(): BelongsTo
    {
        return $this->belongsTo(
            Employees::class,
            'employee_id'
        );
    }


    public function employeeContract(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeContract::class,
            'employee_contract_id'
        );
    }
}
