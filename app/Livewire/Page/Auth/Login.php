<?php

namespace App\Livewire\Page\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Validate([
        'required',
        'email:dns'
    ], message: [
        'email.required' => 'email wajib di isi',
        'email.email' => 'format email tidak valid'
    ])]
    public string $email = '';

    #[Validate([
        'required',
    ], message: [
        'password.required' => 'password wajib di isi',
    ])]
    public string $password = '';
    
    public bool $remember = false;

    public function canSubmit():bool
    {
        return filled($this->email) && filled($this->password) && $this->getErrorBag()->isEmpty();
    }
    public function login(){
        
    }
    
    public function render()
    {
        return view('livewire.page.auth.login');
    }
}
