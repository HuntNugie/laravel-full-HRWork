<?php

namespace App\Livewire\Page\Main\Leave;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Pengajuan cuti karyawan'])]
class LeaveRequest extends Component
{
    public function render()
    {
        return view('livewire.page.main.leave.leave-request');
    }
}
