<?php

namespace App\Livewire\Components\Main\Benefit;

use Livewire\Component;

class FormAdd extends Component
{

    public function canSubmit() {}
    public function render()
    {
        return view('livewire.components.main.benefit.form-add');
    }
}
