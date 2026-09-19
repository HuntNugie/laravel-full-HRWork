<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Payroll Karyawan'])]
class DetailPayrollEmployee extends Component
{
    public PayrollPeriod $period;
    public Payroll $payroll;
    public Employees $employee;
    public EmployeeContract $contract;

    public Collection $items;

    public function mount(
        PayrollPeriod $period,
        Payroll $payroll
    ): void {
        abort_unless(
            Auth::user()->can('show-payroll'),
            403
        );

        abort_unless(
            $payroll->payroll_period_id === $period->id,
            404
        );

        $this->period = $period;

        $this->payroll = $payroll;

        $this->employee = Employees::query()
            ->with('user')
            ->findOrFail($payroll->employee_id);

        $this->contract = EmployeeContract::query()
            ->findOrFail($payroll->employee_contract_id);

        $this->loadItems();
    }

    private function loadItems(): void
    {
        $this->items = PayrollItem::query()
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function earningItems(): Collection
    {
        return $this->items
            ->where('type', 'earning')
            ->values();
    }

    #[Computed]
    public function deductionItems(): Collection
    {
        return $this->items
            ->where('type', 'deduction')
            ->values();
    }

    #[Computed]
    public function totalEarningItems(): float
    {
        return (float) $this->earningItems->sum('amount');
    }

    #[Computed]
    public function totalDeductionItems(): float
    {
        return (float) $this->deductionItems->sum('amount');
    }

    public function statusLabel(): string
    {
        return match ($this->payroll->status) {
            'draft' => 'Draft',
            'processed' => 'Processed',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->payroll->status),
        };
    }

    public function statusIntent(): string
    {
        return match ($this->payroll->status) {
            'draft' => 'warning',
            'processed' => 'primary',
            'paid' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function money(float|int|string|null $value): string
    {
        return 'Rp' . number_format(
            (float) ($value ?? 0),
            0,
            ',',
            '.'
        );
    }

    public function render(): View
    {
        return view(
            'livewire.page.main.payroll.detail-payroll-employee'
        );
    }
}
