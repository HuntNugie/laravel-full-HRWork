<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Surat Peringatan Saya'])]
class MyWarningLetters extends Component
{
    use WithPagination;

    public Employees $employee;

    public int $perPage = 10;

    public function mount(): void
    {
        $this->employee = Auth::user()
            ->employees()
            ->with('user')
            ->firstOrFail();
    }

    #[Computed]
    public function warningLetters()
    {
        return EmployeeWarningLetter::query()
            ->with('issuer')
            ->where('employee_id', $this->employee->id)
            ->where('status', 'issued')
            ->latest('issued_date')
            ->latest('id')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function summary(): array
    {
        $query = EmployeeWarningLetter::query()
            ->where('employee_id', $this->employee->id)
            ->where('status', 'issued');

        return [
            'total' => (clone $query)->count(),
            'sp1' => (clone $query)->where('warning_level', 'SP1')->count(),
            'sp2' => (clone $query)->where('warning_level', 'SP2')->count(),
            'sp3' => (clone $query)->where('warning_level', 'SP3')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.page.main.employee.my-warning-letters');
    }
}
