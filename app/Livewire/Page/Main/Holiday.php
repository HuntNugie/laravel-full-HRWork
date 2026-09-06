<?php

namespace App\Livewire\Page\Main;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman manajemen libur'])]
class Holiday extends Component
{
    public function render()
    {
        return view('livewire.page.main.holiday');
    }
}
