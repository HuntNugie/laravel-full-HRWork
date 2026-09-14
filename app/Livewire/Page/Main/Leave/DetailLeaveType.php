<?php

namespace App\Livewire\Page\Main\Leave;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman detail jenis cuti'])]
class DetailLeaveType extends Component
{
    public function render()
    {
        return view('livewire.page.main.leave.detail-leave-type');
    }
}
