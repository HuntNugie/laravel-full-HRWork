<?php
namespace App\Livewire\Page\Main\Employee\Contract;
use App\Models\Benefit;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\Position;
use App\Service\ContractService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Edit Kontrak Draft'])]
class EditEmployeeContract extends Component
{
    public EditEmployeeContractForm $form;
    public EmployeeContract $contract;

    public Employees $employee;
    public array $positions = [];
    public Collection $benefits;
    public Collection $leaveType;
    public bool $is_active = false;
    public string $position_name = '';
    public string $contract_number = '';
    private function loadOptions(): void
    {
        $this->positions = Position::where('is_active', 'active')->pluck('name', 'id')->all();
        $this->benefits = Benefit::where('status', 'active')->get();
        $this->leaveType = LeaveType::where('status', 'active')->whereIn('gender', ['all', $this->employee->profile?->gender])->get();
    }
    public function updatedFormPositionId($value): void
    {
        $position = Position::where('is_active', 'active')->find($value);
        $this->form->salary_position = $position?->min_salary_daily ?? 0;
        $this->form->salary_daily = $position?->min_salary_daily ?? 0;
        $this->position_name = $position?->name ?? '';
    }
    public function updatedFormContractType($value): void
    {
        $this->is_active = $value === 'pkwtt';
        if ($this->is_active) { $this->form->end_date = null; }
    }
    #[Computed] public function workingDays(): int { return app(ContractService::class)->workingDays($this->form->start_date); }
    #[Computed] public function totalBenefitDay(): float
    {
        return collect($this->form->benefitSelect)->filter(fn ($benefit) => $benefit['selected'] ?? false)->sum(fn ($benefit) => (float) ($benefit['amount'] ?? 0));
    }
    #[Computed] public function totalSalaryMonth(): float { return (float) $this->form->salary_daily * $this->workingDays; }
    #[Computed] public function totalBenefitMonth(): float { return $this->totalBenefitDay * $this->workingDays; }
    #[Computed] public function totalBenefitSalaryDay(): float { return (float) $this->form->salary_daily + $this->totalBenefitDay; }
    #[Computed] public function totalBenefitSalaryMonth(): float { return $this->totalBenefitSalaryDay * $this->workingDays; }
    private function redirectAfterSave(EmployeeContract $contract): void
    {
        session()->flash('success', 'Kontrak berhasil disimpan.');
        if (auth()->user()->can('show-contract')) {
            $this->redirectRoute('contract.show', [$contract->employee_id, $contract->id], navigate: true);
        } else {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function mount(Employees $employee): void
    {
        $this->employee = $employee->load(['profile', 'media']);
        $this->contract = $employee->employeeContract()->where('status', 'draft')->latest('id')->firstOrFail();
        $this->authorize('update', $this->contract);
        $this->loadOptions();
        $this->contract_number = $this->contract->contract_number;
        $this->form->positionId = $this->contract->position_id;
        $this->position_name = $this->contract->position_name;
        $this->form->salary_position = $this->contract->position?->min_salary_daily ?? 0;
        $this->form->salary_daily = $this->contract->salary_daily;
        $this->form->contractType = $this->contract->employement_type;
        $this->is_active = $this->form->contractType === 'pkwtt';
        $this->form->start_date = $this->contract->start_date->format('Y-m-d');
        $this->form->end_date = $this->contract->end_date?->format('Y-m-d');
        $this->form->statusContract = 'draft';
        $this->form->note = $this->contract->notes ?? '';
        $this->form->benefitSelect = $this->contract->benefits->where('status', 'active')->mapWithKeys(fn ($benefit) => [$benefit->id => ['selected' => true, 'amount' => $benefit->pivot->amount]])->toArray();
        $this->form->dayLeave = $this->leaveType->mapWithKeys(fn ($leave) => [$leave->id => $this->contract->contractLeave->firstWhere('leave_type_id', $leave->id)?->days ?? $leave->default_days])->toArray();
    }
    public function save(): void
    {
        $this->validate();
        $this->redirectAfterSave(app(ContractService::class)->save($this->employee, $this->form->all(), $this->contract));
    }
    public function render() { return view('livewire.page.main.employee.contract.edit-employee-contract'); }
}
