<?php
namespace App\Livewire\Page\Main\Employee\Contract;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Service\ContractService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.main', ['title' => 'Detail Kontrak'])]
class DetailEmployeeContract extends Component
{
    public Employees $employee;
    public EmployeeContract $contract;
    public Collection $benefits;
    public Collection $leaveEntitlements;
    public string $reason = '';
    public function mount(Employees $employee, EmployeeContract $contract): void
    {
        abort_unless(auth()->user()->can('show-contract'), 403);
        abort_unless($contract->employee_id === $employee->id, 404);
        $this->employee = $employee;
        $this->contract = $contract;
        $this->benefits = $contract->benefits;
        $this->leaveEntitlements = $contract->contractLeave()->with('leaveType')->get();
    }
    #[Computed] public function totalBenefits(): float { return $this->benefits->sum('pivot.amount'); }
    public function terminate(): void
    {
        $this->validate(['reason' => 'required|string|max:1000']);
        app(ContractService::class)->terminate($this->contract, $this->reason);
        $this->contract->refresh();
        $this->employee->refresh();
        $this->reset('reason');
    }
    public function render() { return view('livewire.page.main.employee.contract.detail-employee-contract'); }
}
