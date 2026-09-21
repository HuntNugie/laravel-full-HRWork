<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Slip Gaji Saya'])]
class DetailMyPayroll extends Component
{
    public Payroll $payroll;

    public PayrollPeriod $period;

    public Employees $employee;

    public Collection $items;

    public function mount(Payroll $payroll): void
    {
        /** @var Employees|null $employee */
        $employee = Auth::user()->employees;

        abort_unless($employee, 403);

        abort_unless(
            (int) $payroll->employee_id === (int) $employee->id,
            404
        );

        abort_unless(
            in_array($payroll->status, ['processed', 'paid'], true),
            404
        );

        $payroll->load('period');

        abort_unless(
            $payroll->period
            && in_array($payroll->period->status, ['processed', 'paid'], true),
            404
        );

        $this->employee = $employee;
        $this->payroll = $payroll;
        $this->period = $payroll->period;

        $this->items = $payroll->items()
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

    public function statusLabel(): string
    {
        return match ($this->payroll->status) {
            'processed' => 'Selesai Diproses',
            'paid' => 'Dibayar',
            default => ucfirst($this->payroll->status),
        };
    }

    public function statusIntent(): string
    {
        return match ($this->payroll->status) {
            'processed' => 'primary',
            'paid' => 'success',
            default => 'neutral',
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
            'livewire.page.main.payroll.detail-my-payroll'
        );
    }
}
