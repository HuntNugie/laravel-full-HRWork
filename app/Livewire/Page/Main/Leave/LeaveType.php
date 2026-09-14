<?php

namespace App\Livewire\Page\Main\Leave;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman jenis cuti'])]
class LeaveType extends Component
{
    public function render()
    {
        return view('livewire.page.main.leave.leave-type');
    }
}
