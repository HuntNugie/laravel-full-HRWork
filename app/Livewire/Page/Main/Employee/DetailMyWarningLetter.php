<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Surat Peringatan'])]
class DetailMyWarningLetter extends Component
{
    public Employees $employee;

    public EmployeeWarningLetter $warningLetter;

    public function mount(EmployeeWarningLetter $warningLetter): void
    {
        $this->employee = Auth::user()
            ->employees()
            ->firstOrFail();

        $this->warningLetter = EmployeeWarningLetter::query()
            ->with(['issuer', 'employee.user'])
            ->whereKey($warningLetter->id)
            ->where('employee_id', $this->employee->id)
            ->where('status', 'issued')
            ->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.page.main.employee.detail-my-warning-letter');
    }
}
