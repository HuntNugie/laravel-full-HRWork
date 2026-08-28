<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Employees;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FormAssignSupervisor extends Component
{
    public ?Team $team;

    public string $search = "";
    public int $employeeId = 0;

    public function updateForm()
    {
        $this->validate([
            "employeeId" => "required|exists:employees,id"
        ]);

        DB::transaction(function () {
            // jika team ini sudah punya supervisor
            if ($this->team->supervisor) {
                $this->team->supervisor?->update(['team_id' => null]);
            }
            $this->team->update(['supervisor_id' => $this->employeeId]);
            Employees::find($this->employeeId)->update(['team_id' => $this->team->id]);
        });

        $this->dispatch("wirekit-modal-close", name: "update-supervisor");
        $this->dispatch("update-team");
    }
    public function render()
    {
        $supervisors = Employees::with(['user'])->whereDoesntHave("team")->whereHas("position", function ($q) {
            return $q->where("name", "=", "supervisor");
        })->where("id", "!=", $this->team->supervisor->id ?? 0)
            ->when($this->search, function ($q) {
                return $q->where(function ($qe) {
                    $qe->whereHas("user", function ($qd) {
                        return $qd->where("name", "LIKE", "%" . $this->search . "%");
                    })->orWhere("employee_code", "LIKE", "%" . $this->search . "%");
                });
            })
            ->latest()->get();

        return view('livewire.components.main.team.form-assign-supervisor', compact('supervisors'));
    }
}
