<?php

namespace App\Livewire\Components\Main;

use App\Service\AuthService;
use Livewire\Component;

class BtnLogout extends Component
{
    public function Logout(){
        AuthService::logout();
        $this->redirectRoute("login",navigate:true);
    }
    public function render()
    {
        return view('livewire.components.main.btn-logout');
    }
}
