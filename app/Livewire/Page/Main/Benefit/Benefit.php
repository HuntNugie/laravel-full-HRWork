<?php

namespace App\Livewire\Page\Main\Benefit;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Benefit Management'])]
class Benefit extends Component
{
    public function render()
    {
        return view('livewire.page.main.benefit.benefit');
    }
}
