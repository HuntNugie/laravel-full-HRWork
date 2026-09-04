<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Roles'])]
class Roles extends Component
{
    public function render()
    {
        return view('livewire.page.main.roles.roles');
    }
}
