<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveType;
use Livewire\Component;

class TypeFormEdit extends Component
{
    public LeaveType $leaveType;
    public string $nameLeave = "";
    public string $defaultDay = "";
    public string $genderLeave = "all";
    public string $descriptionLeave = "";
    public bool $status = true;

    public function mount()
    {
        $this->nameLeave = $this->leaveType->name;
        $this->defaultDay = $this->leaveType->default_days;
        $this->genderLeave = $this->leaveType->gender;
        $this->descriptionLeave = $this->leaveType->description;
        $this->status = $this->leaveType->status === 'active' ? true : false;
    }
    public function save()
    {
        $this->validate([
            'nameLeave' => ['required', 'string'],
            'defaultDay' => ['required', 'numeric'],
            'genderLeave' => ['required', 'in:all,male,female'],
            'descriptionLeave' => ['required', 'string'],
        ], [
            'nameLeave.required' => "Nama cuti wajib di isi",
            'defaultDay.required' => "Jatah harian wajib di isi",
            'defaultDay.numeric' => "Jatah harian harus berupa angka",
            'genderLeave.required' => "karyawan wajib di pilih",
            'genderLeave.in' => "karyawan tidak ada di pilihan",
            'descriptionLeave.required' => "deskripsi cuti wajib di isi"
        ]);

        $this->leaveType->update([
            'name' => $this->nameLeave,
            'gender' => $this->genderLeave,
            'default_days' => $this->defaultDay,
            'description' => $this->descriptionLeave,
            'status' => $this->status ? 'active' : 'inactive',
        ]);

        $this->dispatch('wirekit-modal-close', name: 'edit-leave-type');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: "berhasil mengedit jenis cuti");
        $this->dispatch('change-leave-type');
    }
    public function render()
    {
        return view('livewire.components.main.leave.type-form-edit');
    }
}
