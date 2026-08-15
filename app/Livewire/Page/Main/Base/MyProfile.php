<?php

namespace App\Livewire\Page\Main\Base;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title'=>'My Profile'])]
class MyProfile extends Component
{
    public $avatar;
    public function render()
    {
        return view('livewire.page.main.base.my-profile');
    }
}
