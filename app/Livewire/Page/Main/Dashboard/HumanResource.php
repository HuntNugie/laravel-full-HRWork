<?php

namespace App\Livewire\Page\Main\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main')]
class HumanResource extends Component
{
    public function render()
    {
        return view('livewire.page.main.dashboard.human-resource');
    }
}
