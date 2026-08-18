<?php

namespace App\Livewire\Page\Main\Employee;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title'=>'Halaman Karyawan'])]
class Employee extends Component
{
    public function render()
    {
        return view('livewire.page.main.employee.employee');
    }
}
