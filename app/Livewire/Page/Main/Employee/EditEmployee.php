<?php
namespace App\Livewire\Page\Main\Employee;
use App\Models\Employees;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.main', ['title' => 'Edit Karyawan'])]
class EditEmployee extends Component
{
    public EditEmployeeForm $form;
    public Employees $employee;
    public function mount(Employees $employee): void
    {
        $this->authorize('update', $employee);
        $this->employee = $employee;
        $this->form->fullname = $employee->name;
        $this->form->email = $employee->email;
        $profile = $employee->profile;
        $this->form->nik = $profile->nik ?? '';
        $this->form->gender = $profile->gender;
        $this->form->phone = $profile->phone_number ?? '';
        $this->form->birthDate = $profile->birth_date?->format('Y-m-d') ?? '';
        $this->form->birthAddress = $profile->birth_address ?? '';
        $address = $profile->addressProfile;
        $this->form->provinceCode = $address?->village?->district?->regency?->province?->code;
        $this->form->regencyCode = $address?->village?->district?->regency?->code;
        $this->form->districtCode = $address?->village?->district?->code;
        $this->form->villageCode = $address?->village?->code;
        $this->form->detailAddress = $address?->full_address ?? '';
    }
    public function update(): void
    {
        $this->authorize('update', $this->employee);
        $this->validate();
        $this->validate([
            'form.email' => ['required', 'email', Rule::unique('employees', 'email')->ignore($this->employee->id)],
            'form.nik' => ['required', 'digits:16', Rule::unique('employee_profiles', 'nik')->ignore($this->employee->profile->id)],
        ]);
        DB::transaction(function () {
            $this->employee->update(['name' => $this->form->fullname, 'email' => $this->form->email]);
            $this->employee->profile()->update([
                'nik' => $this->form->nik, 'gender' => $this->form->gender, 'phone_number' => $this->form->phone,
                'birth_date' => $this->form->birthDate, 'birth_address' => $this->form->birthAddress,
            ]);
            $this->employee->profile->addressProfile()->updateOrCreate([], ['full_address' => $this->form->detailAddress, 'village_code' => $this->form->villageCode]);
        });
        session()->flash('success', 'Data karyawan berhasil diperbarui.');
        if (auth()->user()->can('show-employee')) {
            $this->redirectRoute('employee.show', $this->employee->id, navigate: true);
        } else {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }
    public function render() { return view('livewire.page.main.employee.edit-employee'); }
}
