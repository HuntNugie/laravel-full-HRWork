<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\Employees;
use App\Models\MasterProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Master Project'])]
class MasterProjectDetail extends Component
{
    public MasterProject $masterProject;
    public Collection $divisionProjects;

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('view', $masterProject);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $query = $masterProject->divisionProjects()
            ->with([
                'division',
                'manager.user',
                'teams.supervisor.user',
                'tasks.team',
                'tasks.assignee.user',
            ]);

        if ($employee->user?->hasRole('manager')) {
            $query->where('manager_id', $employee->id);
        } elseif ($employee->user?->hasRole('supervisor')) {
            $query->whereHas('teams', fn ($q) => $q->where('supervisor_id', $employee->id));
        } elseif ($employee->user?->hasRole('task-worker')) {
            $query->whereHas('tasks', fn ($q) => $q->where('assignee_id', $employee->id));
        } elseif (! $employee->user?->hasRole('general-manager')) {
            abort(403);
        }

        $this->divisionProjects = $query->get();

        if ($this->divisionProjects->isEmpty()) {
            abort(403);
        }

        $this->masterProject = $masterProject->load([
            'creator.user',
            'approver.user',
        ]);

        $this->masterProject->setRelation('divisionProjects', $this->divisionProjects);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-detail');
    }
}
