<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Employees;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FormAssignEmployee extends Component
{

    public int $teamId;
    public array $employeeId = [];

    public string $search = "";



    public function addEmployees()
    {
        $this->validate([
            "employeeId" => "required|exists:employees,id"
        ]);

        Employees::whereIn('id', $this->employeeId)->update(['team_id' => $this->teamId]);

        $this->dispatch("wirekit-modal-close", name: "assign-employee");
        $this->dispatch("update-team");
    }

    public function canSubmit()
    {
        return true;
    }




    public function render()
    {
        // karyawan yang tidak punya team
        $employees = Employees::with(['user', 'position'])->whereDoesntHave("team")->whereHas("position", function ($q) {
            return $q->whereNotIn("name", ["supervisor", "manager", "general manager"]);
        })->when($this->search, function ($qe) {
            $qe->where(function ($qd) {
                $qd->whereHas("user", function ($qq) {
                    return $qq->where("name", "like", "%" . $this->search . "%");
                })->orWhereHas('profile', function ($qq) {
                    return $qq->where("nik", "like", "%" . $this->search . "%");
                });
            });
        })->latest()->get();
        return view('livewire.components.main.team.form-assign-employee', compact('employees'));
    }
}
