<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\Employees;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Slip Gaji Saya'])]
class MyPayroll extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(Auth::user()->employees, 403);
    }

    private function payrollQuery(): Builder
    {
        /** @var Employees $employee */
        $employee = Auth::user()->employees;

        return Payroll::query()
            ->with('period')
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['processed', 'paid'])
            ->whereHas('period', function (Builder $query) {
                $query->whereIn('status', ['processed', 'paid']);
            })
            ->latest('created_at');
    }

    private function latestPayroll(): ?Payroll
    {
        return $this->payrollQuery()->first();
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

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'processed' => 'Selesai Diproses',
            'paid' => 'Dibayar',
            default => ucfirst($status),
        };
    }

    public function statusIntent(string $status): string
    {
        return match ($status) {
            'processed' => 'primary',
            'paid' => 'success',
            default => 'neutral',
        };
    }

    public function render(): View
    {
        return view(
            'livewire.page.main.payroll.my-payroll',
            [
                'latestPayroll' => $this->latestPayroll(),
                'payrolls' => $this->payrollQuery()->paginate($this->perPage),
            ]
        );
    }
}
