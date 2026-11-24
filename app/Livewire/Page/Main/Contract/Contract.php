<?php
namespace App\Livewire\Page\Main\Contract;
use App\Models\EmployeeContract;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.main', ['title' => 'Manajemen Kontrak'])]
class Contract extends Component
{
    use WithPagination;
    public string $search = '';
    public string $statusFilter = '';
    public string $employmentTypeFilter = '';
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedEmploymentTypeFilter(): void { $this->resetPage(); }
    public function resetFilters(): void { $this->reset(['search', 'statusFilter', 'employmentTypeFilter']); $this->resetPage(); }
    public function render()
    {
        abort_unless(auth()->user()->can('view-contract'), 403);
        $contracts = EmployeeContract::with('employees')
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q->where('contract_number', 'like', '%'.$this->search.'%')->orWhereHas('employees', fn ($e) => $e->where('name', 'like', '%'.$this->search.'%')->orWhere('employee_code', 'like', '%'.$this->search.'%'))))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->employmentTypeFilter, fn ($q) => $q->where('employement_type', $this->employmentTypeFilter))
            ->latest('id')->paginate(10);
        return view('livewire.page.main.contract.contract', compact('contracts'));
    }
}
