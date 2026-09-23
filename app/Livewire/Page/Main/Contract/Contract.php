<?php

namespace App\Livewire\Page\Main\Contract;

use App\Models\EmployeeContract;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' =>  'Halaman Manajemen kontrak'])]
class Contract extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $employmentTypeFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedEmploymentTypeFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'employmentTypeFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employees::query()
            ->whereHas('latestEmployeeContract')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($qe) {
                    $qe->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('latestEmployeeContract', function ($qe) {
                    $qe->where('contract_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->whereHas('latestEmployeeContract', function ($qe) {
                    $qe->where('status', $this->statusFilter);
                });
            })
            ->when($this->employmentTypeFilter, function ($q) {
                $q->whereHas('latestEmployeeContract', function ($qe) {
                    $qe->where('employement_type', $this->employmentTypeFilter);
                });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.page.main.contract.contract', compact('employees'));
    }
}
