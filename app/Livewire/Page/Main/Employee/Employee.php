<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Position;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Karyawan'])]
class Employee extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterDivision = '';

    public string $filterTeam = '';

    public string $filterPosition = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDivision($value): void
    {
        if ($this->filterTeam && ! Team::query()
            ->whereKey($this->filterTeam)
            ->where('divisi_id', $value)
            ->exists()) {
            $this->filterTeam = '';
        }

        $this->resetPage();
    }

    public function updatedFilterTeam(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPosition(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterDivision', 'filterTeam', 'filterPosition']);
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employees::query()
            ->with(['user', 'position', 'team.divisi', 'managedDivisi'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('user', function ($userQuery) {
                        $userQuery
                            ->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })->orWhere('employee_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDivision, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('team', function ($teamQuery) {
                        $teamQuery->where('divisi_id', $this->filterDivision);
                    })->orWhereHas('managedDivisi', function ($divisionQuery) {
                        $divisionQuery->whereKey($this->filterDivision);
                    });
                });
            })
            ->when($this->filterTeam, function ($q) {
                $q->where('team_id', $this->filterTeam);
            })
            ->when($this->filterPosition, function ($q) {
                $q->where('position_id', $this->filterPosition);
            })
            ->latest()
            ->paginate(5);

        $divisions = Divisi::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        $teams = Team::query()
            ->when($this->filterDivision, function ($q) {
                $q->where('divisi_id', $this->filterDivision);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        $positions = Position::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        return view('livewire.page.main.employee.employee', compact(
            'employees',
            'divisions',
            'teams',
            'positions'
        ));
    }
}
