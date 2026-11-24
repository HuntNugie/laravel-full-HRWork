<?php
namespace App\Livewire\Components\Main\Employee;
use App\Models\Bank;
use App\Models\Employees;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
#[Layout('layouts.main', ['title' => 'Tambah Data Karyawan'])]
class CreateEmployee extends Component
{
    use WithFileUploads;
    public CreateEmployeeForm $form;
    public array $banks = [];
    public function mount(): void
    {
        $this->authorize('create', Employees::class);
        $this->banks = Bank::query()->pluck('name', 'id')->toArray();
    }
    public function store(): void
    {
        $this->authorize('create', Employees::class);
        $this->validate();
        if (filled($this->form->bankId) || filled($this->form->accountNumber) || filled($this->form->accountHolder)) {
            if (! filled($this->form->bankId) || ! filled($this->form->accountNumber) || ! filled($this->form->accountHolder)) {
                throw ValidationException::withMessages(['form.bankId' => 'Lengkapi seluruh informasi rekening atau kosongkan semuanya.']);
            }
        }
        $employee = DB::transaction(function () {
            $employee = Employees::create(['name' => $this->form->fullname, 'email' => $this->form->email, 'status_employee' => 'onboarding']);
            $employee->update(['employee_code' => 'EMP-'.$employee->id]);
            $profile = $employee->profile()->create([
                'gender' => $this->form->gender, 'phone_number' => $this->form->phone, 'nik' => $this->form->nik,
                'birth_date' => $this->form->birthDate, 'birth_address' => $this->form->birthAddress,
            ]);
            $profile->addressProfile()->create(['full_address' => $this->form->detailAddress, 'village_code' => $this->form->villageCode]);
            if (filled($this->form->bankId)) {
                $profile->bankAccount()->create(['bank_id' => $this->form->bankId, 'account_number' => $this->form->accountNumber, 'account_holder' => $this->form->accountHolder]);
            }
            $employee->statusHistory()->create(['new_status' => 'onboarding', 'effective_date' => today(), 'reason' => 'Pendaftaran karyawan', 'changed_by' => auth()->id()]);
            return $employee;
        });
        if ($this->form->photo) {
            $employee->addMedia($this->form->photo)->toMediaCollection('avatar');
        }
        session()->flash('success', 'Data karyawan berhasil dibuat tanpa akun login.');
        if (auth()->user()->can('show-employee')) {
            $this->redirectRoute('employee.show', $employee->id, navigate: true);
        } else {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }
    public function cancel(): void { $this->redirectRoute(auth()->user()->can('view-employee') ? 'employee.view' : 'dashboard', navigate: true); }
    public function render() { return view('livewire.components.main.employee.create-employee'); }
}
