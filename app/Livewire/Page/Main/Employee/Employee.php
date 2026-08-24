<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Karyawan'])]
class Employee extends Component
{
    use WithPagination;

    public string $search = '';


    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $employees = Employees::query()->when($this->search, function ($q) {
            return $q->whereHas("user", function ($qu) {
                $qu->where('name', 'LIKE', '%' . $this->search . '%');
            });
        })->latest()->paginate(5);
        return view('livewire.page.main.employee.employee', compact('employees'));
    }
}
