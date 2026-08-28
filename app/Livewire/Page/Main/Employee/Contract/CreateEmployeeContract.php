<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use App\Models\Benefit;
use App\Models\ContractSequence;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\EmployeeStatusHistory;
use App\Models\Position;
use App\Models\Team;
use App\Service\ContractService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Membuat Contract'])]
class CreateEmployeeContract extends Component
{
    public Employees $employee;
    public array $positions;
    public Collection $benefits;
    public array $teams;
    public CreateEmployeeContractForm $form;
    public bool $is_active = false;
    public EmployeeContract $contract;

    public string $position_name = '';

    public string $contract_number = '';
    public function updatedFormPositionId($value)
    {
        $position = Position::findOrFail($value);
        $this->form->salary_position = $position->min_salary_daily;
        $this->form->salary_daily = $position->min_salary_daily;
        $this->position_name = $position->name;
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
    public function mount(Employees $employee)
    {
        $this->employee = $employee->load(['user', 'profile']);
        $this->positions = Position::query()->where('is_active', 'active')->pluck('name', 'id')->toArray();
        $this->benefits = Benefit::all();
        $this->teams = Team::query()->pluck('name', 'id')->toArray();

        $this->contract_number = app(ContractService::class)->previewContractNumber();
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
        return $total * 22;
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
    public function store()
    {
        $this->validate();

        $this->createContract();
        $this->redirectRoute('employee.show', $this->employee->id, navigate: true);
    }

    public function createContract()
    {
        $this->authorize('create', EmployeeContract::class);

        DB::transaction(function () {

            $year = now()->year;
            $month = now()->month;

            $sequence = ContractSequence::query()
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if ($sequence) {
                $sequence->increment('last_number');
                $number = $sequence->last_number;
            } else {
                $number = 1;

                $sequence = ContractSequence::create([
                    'year' => $year,
                    'month' => $month,
                    'last_number' => $number,
                ]);
            }

            $contractNumber = 'CTR/'
                . $year . '/'
                . str_pad($month, 2, '0', STR_PAD_LEFT) . '/'
                . str_pad($number, 3, '0', STR_PAD_LEFT);

            $contract = $this->employee->employeeContract()->create([
                'contract_number' => $contractNumber,
                'position_name' => $this->position_name,
                'employement_type' => $this->form->contractType,
                'start_date' => $this->form->start_date,
                'end_date' => $this->form->end_date,
                'salary_daily' => $this->form->salary_daily,
                'status' => $this->form->statusContract,
                'notes' => $this->form->note,
            ]);

            foreach ($this->form->benefitSelect as $benefitId => $benefit) {
                $contract->benefits()->attach($benefitId, [
                    'amount' => $benefit['amount'],
                ]);
            }

            $this->employee->update(['status_employee' => 'active', 'position_id' => $this->form->positionId]);
            $this->employee->statusHistory()->create([
                'new_status' => 'active',
                'effective_date' => $this->form->start_date,
                'reason' => 'Pembuatan Kontrak'
            ]);

            return $contract;
        });
    }

    public function render()
    {

        return view('livewire.page.main.employee.contract.create-employee-contract');
    }
}
