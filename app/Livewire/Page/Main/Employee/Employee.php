<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use App\Models\Position;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Karyawan'])]
class Employee extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterPosition = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPosition(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterPosition']);
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employees::query()
            ->with(['user', 'position'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('user', function ($userQuery) {
                        $userQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })->orWhere('employee_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPosition, function ($q) {
                $q->where('position_id', $this->filterPosition);
            })
            ->latest()
            ->paginate(5);

        $positions = Position::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        return view('livewire.page.main.employee.employee', compact(
            'employees',
            'positions'
        ));
    }
}
