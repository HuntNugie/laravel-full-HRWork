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

#[Layout('layouts.main', ['title' => 'Buat Kontrak'])]
class CreateEmployeeContract extends Component
{
    public CreateEmployeeContractForm $form;

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
        $this->authorize('create', EmployeeContract::class);
        $this->employee = $employee->load(['profile', 'media']);
        $this->loadOptions();
        $this->contract_number = app(ContractService::class)->previewContractNumber();
        $this->form->dayLeave = $this->leaveType->pluck('default_days', 'id')->toArray();
    }
    public function store(): void
    {
        $this->validate();
        $this->redirectAfterSave(app(ContractService::class)->save($this->employee, $this->form->all()));
    }
    public function render() { return view('livewire.page.main.employee.contract.create-employee-contract'); }
}
