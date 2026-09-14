<?php

namespace App\Livewire\Components\Main\Employee;

use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use Livewire\Component;

class SectionAbsenceHistory extends Component
{
    public Employees $employee;
    public string $segment  = "";

    public ?string $from = null;

    public ?string $to = null;


    public function render()
    {
        $absences = $this->employee->employeeAbsenceRequest()->when($this->segment, function ($query) {
            return $query->where("status", $this->segment);
        })->when($this->from && $this->to, function ($q) {
            return $q->whereBetween('date', [$this->from, $this->to]);
        })->get();
        return view('livewire.components.main.employee.section-absence-history', compact('absences'));
    }
}
