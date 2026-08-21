<?php

namespace App\Livewire\Components\Main\Employee;

use App\Models\Bank;
use App\Models\Position;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Buat akun karyawan'])]
class CreateEmployee extends Component
{
    public CreateEmployeeForm $form;
    public array $teams = [];
    public array $positions = [];
    public array $banks = [];
    public function mount(){
        $this->teams = Team::query()->pluck('name','id')->toArray();
        $this->positions = Position::query()->pluck('name','id')->toArray();
        $this->banks = Bank::query()->pluck('name','id')->toArray();
        
    }
    public function render()
    {
        return view('livewire.components.main.employee.create-employee');
    }
}
