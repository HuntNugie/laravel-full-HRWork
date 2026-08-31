<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Employees;
use Livewire\Component;

class RemoveEmployee extends Component
{
    public Employees $employee;

    public function removeEmployee()
    {
        $this->employee->update([
            'team_id' => null
        ]);

        $this->dispatch('wirekit-modal-close', name: 'remove-employee-team');
        $this->dispatch('update-team');
    }
    public function render()
    {
        return view('livewire.components.main.team.remove-employee');
    }
}
