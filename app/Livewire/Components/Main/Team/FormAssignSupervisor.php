<?php

namespace App\Livewire\Components\Main\Team;

use App\Models\Employees;
use App\Models\Team;
use App\Service\OrganizationAssignmentService;
use Livewire\Component;

class FormAssignSupervisor extends Component
{
    public ?Team $team;

    public string $search = "";

    public int $employeeId = 0;

    public function updateForm()
    {

        $this->validate([
            "employeeId" => "required|exists:employees,id",
        ]);

        app(OrganizationAssignmentService::class)->assignSupervisor(
            $this->team,
            Employees::findOrFail($this->employeeId),
        );

        $this->dispatch(
            "wirekit-modal-close",
            name: "update-supervisor"
        );

        $this->dispatch("update-team");
    }

    public function render()
    {
        $supervisors = Employees::with(['user', 'position'])
            ->where(function ($query) {

                // Employee yang belum punya team
                $query->whereDoesntHave('team')

                    // ATAU employee yang sudah menjadi
                    // member team ini
                    ->orWhere('team_id', $this->team->id);
            })

            // Jangan tampilkan supervisor yang sedang aktif
            ->where('id', '!=', $this->team->supervisor_id)

            ->when($this->search, function ($query) {

                $query->where(function ($q) {

                    $q->whereHas('user', function ($q) {
                        $q->where(
                            'name',
                            'like',
                            '%' . $this->search . '%'
                        );
                    })

                        ->orWhere(
                            'employee_code',
                            'like',
                            '%' . $this->search . '%'
                        )->orWhereHas('position', function ($qe) {
                            $qe->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })

            ->latest()
            ->get();

        return view(
            'livewire.components.main.team.form-assign-supervisor',
            compact('supervisors')
        );
    }
}
