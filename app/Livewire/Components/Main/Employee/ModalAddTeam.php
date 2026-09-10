<?php

namespace App\Livewire\Components\Main\Employee;

use App\Models\Employees;
use App\Models\Team;
use Livewire\Component;

class ModalAddTeam extends Component
{
    public Employees $employee;
    public string $selectTeams = "";
    public ?Employees $supervisor = null;
    public ?Team $teamSelected = null;
    public function updatedSelectTeams(Team $team): void
    {
        $this->teamSelected = $team->load(['supervisor']);
        $this->supervisor = $this->teamSelected->supervisor;
    }

    public function changeTeam()
    {
        $this->authorize('assignTeam', Employees::class);
        $this->employee->update([
            'team_id' => $this->teamSelected->id
        ]);

        $this->reset([
            'teamSelected',
            'supervisor',
        ]);
        $this->dispatch('wirekit-modal-close', name: 'add-team');
        $this->dispatch("change-team");
        $this->dispatch("wirekit-toast", variant: 'success', title: 'Berhasil menambahkan team', message: "Anda berhasil menambahkan team");
    }

    public function canSubmit()
    {
        return filled($this->teamSelected);
    }

    public function render()
    {
        $teams = Team::with("supervisor")->where("is_active", 'active')->whereHas('supervisor')->when($this->employee?->team, function ($qe) {
            $qe->where('id', "!=", $this->employee->team_id);
        })->pluck("name", "id")->toArray();
        return view('livewire.components.main.employee.modal-add-team', compact("teams"));
    }
}
