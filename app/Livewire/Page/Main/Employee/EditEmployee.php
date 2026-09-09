<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman edit employee'])]
class EditEmployee extends Component
{
    public EditEmployeeForm $form;
    public Employees $employee;
    public function mount(Employees $employee)
    {
        $this->employee = $employee;
        $this->form->fullname = $employee->user->name;
        $this->form->nik = $employee->profile->nik;
        $this->form->gender = $employee->profile->gender;
        $this->form->phone = $employee->profile->phone_number;
        $this->form->provinceCode = $employee->profile->addressProfile->village->district->regency->province->code;
        $this->form->regencyCode = $employee->profile->addressProfile->village->district->regency->code;
        $this->form->districtCode = $employee->profile->addressProfile->village->district->code;
        $this->form->villageCode = $employee->profile->addressProfile->village->code;

        $this->form->detailAddress = $employee->profile->addressProfile->full_address;
    }


    public function update()
    {
        $this->authorize("update", $this->employee);
        // update
        DB::transaction(function () {
            // update untuk user
            $this->employee->user()->update([
                'name' => $this->form->fullname
            ]);

            // update untuk profile
            $this->employee->profile()->update([
                'nik' => $this->form->nik,
                'gender' => $this->form->gender,
                'phone_number' => $this->form->phone,
            ]);

            // update address
            $this->employee->profile->addressProfile()->update([
                'full_address' => $this->form->detailAddress,
                'village_code' => $this->form->villageCode
            ]);
        });

        session()->flash('success', 'Berhasil mengedit data employee');
        $this->redirectRoute('employee.show', $this->employee->id, navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.employee.edit-employee');
    }
}
