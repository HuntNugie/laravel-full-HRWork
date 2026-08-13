<?php

namespace App\Livewire\Page\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    public function render()
    {
        return view('livewire.page.auth.login');
    }
}
