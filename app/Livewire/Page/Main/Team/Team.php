<?php

namespace App\Livewire\Page\Main\Team;

use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.main',['title'=>'Halaman Team'])]
class Team extends Component
{
    public function render()
    {
        return view('livewire.page.main.team.team');
    }
}
