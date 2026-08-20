<?php

namespace App\Livewire\Components\Main\Employee;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title'=>'Halaman Buat akun karyawan'])]
class CreateEmployee extends Component
{
    public function render()
    {
        return view('livewire.components.main.employee.create-employee');
    }
}
