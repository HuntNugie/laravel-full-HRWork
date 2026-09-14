<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveType;
use Livewire\Component;

class TypeFormAdd extends Component
{
    public string $nameLeave = "";
    public string $defaultDay = "";
    public string $genderLeave = "all";
    public string $descriptionLeave = "";
    public bool $status = true;

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

        $leave = LeaveType::create([
            'name' => $this->nameLeave,
            'gender' => $this->genderLeave,
            'default_days' => $this->defaultDay,
            'description' => $this->descriptionLeave,
            'status' => $this->status ? 'active' : 'inactive',
        ]);

        $this->dispatch('wirekit-modal-close', name: 'create-leave-type');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: "berhasil membuat jenis cuti");
        $this->dispatch('change-leave-type');
    }
    public function render()
    {
        return view('livewire.components.main.leave.type-form-add');
    }
}
