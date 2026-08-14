<?php

namespace App\Livewire\Page\Auth;

use App\Service\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth',["title" => "Halaman Login"])]
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
    public function authenticate(){
        $result = AuthService::login($this->email,$this->password,$this->remember);
        if( !$result['result']){
            $this->addError("login_error",$result["message"]);
            return;
        }
        $this->redirectRoute("dashboard",navigate:true);

    }
    
    public function render()
    {
        return view('livewire.page.auth.login');
    }
}
