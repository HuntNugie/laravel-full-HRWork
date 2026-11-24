<?php
namespace App\Livewire\Page\Main\Employee;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
#[Layout('layouts.main', ['title' => 'Detail Karyawan'])]
class DetailEmployee extends Component
{
    public Employees $employee;
    public function mount(Employees $employee): void { $this->employee = $employee; $this->refreshEmployee(); }
    #[On('change-photo')]
    #[On('change-employee')]
    public function refreshEmployee(): void
    {
        abort_unless(auth()->user()->can('show-employee'), 403);
        $this->employee->refresh()->load(['position', 'profile.addressProfile.village.district.regency.province', 'profile.bankAccount.bank', 'media', 'statusHistory']);
    }
    public function render()
    {
        abort_unless(auth()->user()->can('show-employee'), 403);
        $contracts = auth()->user()->can('show-contract') ? $this->employee->employeeContract()->latest('id')->get() : collect();
        return view('livewire.page.main.employee.detail-employee', compact('contracts'));
    }
}
