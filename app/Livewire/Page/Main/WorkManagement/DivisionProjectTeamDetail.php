<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\Task;
use App\Models\Team;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Team Work Board'])]
class DivisionProjectTeamDetail extends Component
{
    public DivisionProject $divisionProject;
    public Team $team;
    public Collection $tasks;
    public Collection $members;

    public function mount(DivisionProject $divisionProject, Team $team): void
    {
        $this->authorize('viewTeam', [$divisionProject, $team]);

        if (! $divisionProject->teams()->whereKey($team->id)->exists()) {
            abort(404);
        }

        $this->divisionProject = $divisionProject;
        $this->team = $team;
        $this->loadPage();
    }

    public function toggleTask(Task $task, WorkManagementService $service, bool $completed): void
    {
        $this->authorize('updateOwn', $task);

        if ((int) $task->team_id !== (int) $this->team->id) {
            abort(404);
        }

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->toggleTaskCompletion($task, $employee, $completed);
        $this->loadPage();
    }

    public function render()
    {
        return view('livewire.page.main.work-management.division-project-team-detail');
    }

    public function getCompletionLabelProperty(): string
    {
        $activeTasks = $this->tasks->reject(
            fn (Task $task) => $task->status === Task::STATUS_CANCELLED
        );

        if ($activeTasks->isEmpty()) {
            return '0 / 0 selesai';
        }

        return $activeTasks->where('status', Task::STATUS_DONE)->count()
            . ' / '
            . $activeTasks->count()
            . ' selesai';
    }

    private function loadPage(): void
    {
        $this->team = $this->team->refresh()->load([
            'supervisor.user',
            'employees.user',
        ]);

        $this->members = $this->team->employees
            ->sortBy(fn ($employee) => (int) $employee->id)
            ->values();

        $this->tasks = $this->divisionProject->tasks()
            ->where('team_id', $this->team->id)
            ->with('assignee.user')
            ->orderByDesc('id')
            ->get();
    }
}
