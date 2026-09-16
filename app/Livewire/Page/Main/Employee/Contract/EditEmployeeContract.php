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

#[Layout('layouts.main', ['title' => 'Halaman Edit kontrak draft'])]
class EditEmployeeContract extends Component
{
    public Employees $employee;
    public EmployeeContract $contract;
    public EditEmployeeContractForm $form;
    public array $positions = [];
    public string $position_name = '';
    public bool $is_active = false;
    public string $contract_number = '';
    public ?Collection $benefits = null;
    public ?Collection $leaveType = null;

    public function mount(Employees $employee)
    {
        $this->authorize('update', $employee->latestEmployeeContract);
        $this->employee = $employee;
        $this->contract = $employee->latestEmployeeContract;
        $this->positions = Position::query()->where('is_active', 'active')->pluck('name', 'id')->toArray();
        $this->benefits = Benefit::where('status', '=', 'active')->get();


        $this->contract_number = $this->contract->contract_number;
        $this->form->positionId = $employee?->position?->id;
        $this->form->salary_position = $employee?->position?->min_salary_daily;
        $this->form->salary_daily = $this->contract->salary_daily;
        $this->form->contractType = $this->contract->employement_type;
        $this->is_active = $this->form->contractType === 'pkwtt';
        $this->form->start_date = $this->contract->start_date->format('Y-m-d');
        $this->form->end_date = $this->is_active ? null : $this->contract->end_date?->format('Y-m-d');
        $this->form->statusContract = $this->contract->status;
        $this->form->benefitSelect = $this->contract->benefits
            ->mapWithKeys(function ($benefit) {
                return [
                    $benefit->id => [
                        'selected' => true,
                        'amount' => intval($benefit->pivot->amount),
                    ],
                ];
            })
            ->toArray();
        $this->leaveType = LeaveType::query()->where('status', 'active')->where(function ($q) {
            $q->where('gender', $this->employee->profile->gender)->orWhere('gender', 'all');
        })->get();
        $this->form->dayLeave = $this->contract?->contractLeave ? $this->contract?->contractLeave()->pluck('days', 'leave_type_id')->toArray() : $this?->leaveType?->pluck('default_days', 'id')->toArray();
        $this->form->note = $this->contract->notes;
    }

    #[Computed]
    public function totalBenefitSalaryMonth()
    {
        $total = $this->form->salary_daily;
        foreach ($this->form->benefitSelect as $benefit) {
            if ($benefit['selected'] ?? false) {
                $total += $benefit['amount'] ?? 0;
            }
        }
        return $total * 24;
    }

    #[Computed]
    public function totalBenefitSalaryDay()
    {
        $total = $this->form->salary_daily;
        foreach ($this->form->benefitSelect as $benefit) {
            if ($benefit['selected'] ?? false) {
                $total += $benefit['amount'] ?? 0;
            }
        }
        return $total;
    }

    #[Computed]
    public function totalSalaryMonth()
    {
        return $this->form->salary_daily * 24;
    }

    #[Computed]
    public function totalBenefitDay()
    {
        $total = 0;
        foreach ($this->form->benefitSelect as $benefit) {
            if ($benefit['selected'] ?? false) {
                $total += $benefit['amount'] ?? 0;
            }
        }
        return $total;
    }
    #[Computed]
    public function totalBenefitMonth()
    {
        $total = 0;
        foreach ($this->form->benefitSelect as $benefit) {
            if ($benefit['selected'] ?? false) {
                $total += $benefit['amount'] ?? 0;
            }
        }
        return $total * 24;
    }

    public function updatedFormContractType($value)
    {
        if ($value === "pkwtt") {
            $this->is_active = true;
            $this->form->end_date = null;
        } else {
            $this->is_active = false;
        }
    }

    public function updatedFormPositionId($value)
    {
        $position = Position::findOrFail($value);
        $this->form->salary_position = $position->min_salary_daily;
        $this->form->salary_daily = $position->min_salary_daily;
        $this->position_name = $position->name;
    }

    public function render()
    {
        return view('livewire.page.main.employee.contract.edit-employee-contract');
    }
}
