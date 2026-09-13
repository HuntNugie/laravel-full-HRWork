<?php

namespace App\Livewire\Page\Main\Absence;

use App\Models\EmployeeAbsenceRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman approved sakit/izin'])]
class Absence extends Component
{
    use WithPagination;
    public string $search = '';
    public string $segment = '';
    public string $typeLeave = '';

    #[On('update-absence')]
    public function refreshAbsence() {}

    public function render()
    {
        $absences = EmployeeAbsenceRequest::query()->when($this->search, function ($q) {
            $q->whereHas('employees', function ($q) {
                $q->whereHas('user', function ($qw) {
                    $qw->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('employee_code', 'like', '%' . $this->search . '%');
            });
        })->when($this->segment, function ($q) {
            $q->where('status', $this->segment);
        })->when($this->typeLeave, function ($q) {
            $q->where('type', $this->typeLeave);
        })->latest()->paginate(5);

        return view('livewire.page.main.absence.absence', compact('absences'));
    }
}
