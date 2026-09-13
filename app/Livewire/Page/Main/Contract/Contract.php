<?php

namespace App\Livewire\Page\Main\Contract;

use App\Models\EmployeeContract;
use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' =>  'Halaman Manajemen kontrak'])]
class Contract extends Component
{
    use WithPagination;

    public function render()
    {
        $employees = Employees::query()->whereHas('latestEmployeeContract')->latest()->paginate(5);
        return view('livewire.page.main.contract.contract', compact('employees'));
    }
}
