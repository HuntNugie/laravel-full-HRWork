<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Membuat Contract'])]
class CreateEmployeeContract extends Component
{

    public function render()
    {
        return view('livewire.page.main.employee.contract.create-employee-contract');
    }
}
