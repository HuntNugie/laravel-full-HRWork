<?php

namespace App\Livewire\Page\Main\Attendances;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.main", ['title' => "Halaman presensi"])]
class Attendances extends Component
{
    public function render()
    {
        return view('livewire.page.main.attendances.attendances');
    }
}
