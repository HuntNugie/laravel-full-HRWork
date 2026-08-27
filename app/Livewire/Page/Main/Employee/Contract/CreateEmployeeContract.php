<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use App\Models\Benefit;
use App\Models\Employees;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Membuat Contract'])]
class CreateEmployeeContract extends Component
{
    public Employees $employee;
    public array $positions;
    public Collection $benefits;
    public array $teams;

    public $positionId = '';

    public $salary_position = 0;

    public function updatedPositionId($value)
    {
        $position = Position::find($value);
        $this->salary_position = $position->min_salary_daily;
    }

    public function mount(Employees $employee)
    {
        $this->employee = $employee->load(['user', 'profile']);
        $this->positions = Position::query()->where('is_active', 'active')->pluck('name', 'id')->toArray();
        $this->benefits = Benefit::all();
        $this->teams = Team::query()->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.page.main.employee.contract.create-employee-contract');
    }
}
