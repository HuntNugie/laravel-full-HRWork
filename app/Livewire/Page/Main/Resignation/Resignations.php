<?php

namespace App\Livewire\Page\Main\Resignation;

use App\Models\EmployeeResignation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Manajemen Resignation'])]
class Resignations extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public int $perPage = 10;

    #[Computed]
    public function summary(): array
    {
        return [
            'submitted' => EmployeeResignation::query()->where('status', 'submitted')->count(),
            'approved' => EmployeeResignation::query()->where('status', 'approved')->count(),
            'completed' => EmployeeResignation::query()->where('status', 'completed')->count(),
            'rejected' => EmployeeResignation::query()->where('status', 'rejected')->count(),
        ];
    }

    #[Computed]
    public function resignations()
    {
        return EmployeeResignation::query()
            ->with(['employee.user', 'employee.position', 'employee.team.divisi'])
            ->when(filled($this->search), function (Builder $query) {
                $search = trim($this->search);

                $query->whereHas('employee', function (Builder $employeeQuery) use ($search) {
                    $employeeQuery->where('employee_code', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->status !== 'all', fn(Builder $query) => $query->where('status', $this->status))
            ->latest('id')
            ->paginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.page.main.resignation.resignations');
    }
}
