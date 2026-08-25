<?php

namespace App\Livewire\Components\Main\Employee;

use App\Models\Bank;
use App\Models\Employees;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Buat akun karyawan'])]
class CreateEmployee extends Component
{
    public ?User $user;
    public CreateEmployeeForm $form;
    public array $banks = [];
    public function mount()
    {
        $this->banks = Bank::query()->pluck('name', 'id')->toArray();
    }


    public function store()
    {
        $this->createUser();
        $this->createEmployee();

        $this->redirectRoute('employee.view', navigate: true);
    }

    protected function createUser()
    {
        $this->authorize('create', User::class);

        $user = [
            'name' => $this->form->fullname,
            'email' => $this->form->email,
            'password' => bcrypt($this->form->password),
        ];

        $this->user = User::create($user);
    }

    protected function createEmployee()
    {
        $this->authorize('create', Employees::class);
        $employee = $this->user->employees()->create();

        $employee->update(['employee_code' => 'EMP-' . $employee->id]);

        //    isi profile nya
        $profile =  $employee->profile()->create([
            'gender' => $this->form->gender,
            'phone_number' => $this->form->phone,
            'nik' => $this->form->nik,
        ]);

        //    isi profile address
        $profile->addressProfile()->create([
            'full_address' => $this->form->detailAddress,
            'village_code' => $this->form->villageCode
        ]);

        //    isi bank
        $profile->bankAccount()->create([
            'bank_id' => $this->form->bankId,
            'account_number' => $this->form->accountNumber,
            'account_holder' => $this->form->accountHolder,
        ]);
    }
    public function render()
    {
        return view('livewire.components.main.employee.create-employee');
    }
}
