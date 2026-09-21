<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\Employees;
use App\Models\MasterProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Master Projects'])]
class MasterProjects extends Component
{
    public Collection $projects;

    public function mount(): void
    {
        $this->authorize('viewAny', MasterProject::class);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $query = MasterProject::query()
            ->with(['creator.user', 'divisionProjects'])
            ->latest();

        $this->applyScope($query, $employee);
        $this->projects = $query->get();
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-projects');
    }

    private function applyScope($query, Employees $employee): void
    {
        if ($employee->user?->hasRole('general-manager')) {
            return;
        }

        if ($employee->user?->hasRole('manager')) {
            $query->whereHas('divisionProjects', fn ($q) => $q->where('manager_id', $employee->id));
            return;
        }

        if ($employee->user?->hasRole('supervisor')) {
            $query->whereHas('divisionProjects.teams', fn ($q) => $q->where('supervisor_id', $employee->id));
            return;
        }

        if ($employee->user?->hasRole('task-worker')) {
            $query->whereHas('tasks', fn ($q) => $q->where('assignee_id', $employee->id));
            return;
        }

        $query->whereKey(0);
    }
}
