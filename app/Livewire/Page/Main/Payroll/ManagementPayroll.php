<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Penggajian'])]
class ManagementPayroll extends Component
{
    use WithPagination;

    public string $month = 'all';

    public string $year;

    public string $status = 'all';

    public int $perPage = 10;


    public function mount(): void
    {
        $this->year = (string) now()->year;
    }


    public function updatedMonth(): void
    {
        $this->resetPage();
    }


    public function updatedYear(): void
    {
        $this->resetPage();
    }


    public function updatedStatus(): void
    {
        $this->resetPage();
    }


    public function resetFilters(): void
    {
        $this->month = 'all';
        $this->year = (string) now()->year;
        $this->status = 'all';

        $this->resetPage();
    }


    private function payrollPeriodQuery(): Builder
    {
        return PayrollPeriod::query()

            ->when(
                $this->month !== 'all',
                fn(Builder $query) =>
                $query->whereMonth(
                    'start_date',
                    (int) $this->month
                )
            )

            ->when(
                filled($this->year),
                fn(Builder $query) =>
                $query->whereYear(
                    'start_date',
                    (int) $this->year
                )
            )

            ->when(
                $this->status !== 'all',
                fn(Builder $query) =>
                $query->where(
                    'status',
                    $this->status
                )
            )

            ->latest('start_date');
    }

    #[On('payroll-period-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view(
            'livewire.page.main.payroll.management-payroll',
            [
                'payrollPeriods' => $this
                    ->payrollPeriodQuery()
                    ->paginate($this->perPage),
            ]
        );
    }
}
