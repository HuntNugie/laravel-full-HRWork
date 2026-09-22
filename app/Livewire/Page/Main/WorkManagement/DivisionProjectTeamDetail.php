<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\ProjectReport;
use App\Models\Task;
use App\Models\Team;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Team Progress'])]
class DivisionProjectTeamDetail extends Component
{
    public DivisionProject $divisionProject;
    public Team $team;
    public Collection $tasks;
    public ?ProjectReport $latestSupervisorReport = null;

    public string $supervisorReport = '';

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

    public function submitSupervisorReport(WorkManagementService $service): void
    {
        $this->authorize('submitSupervisorReport', $this->divisionProject);

        $this->validate([
            'supervisorReport' => ['required', 'string'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee || (int) $this->team->supervisor_id !== (int) $employee->id) {
            abort(403);
        }

        $service->submitSupervisorReport(
            $this->divisionProject,
            $employee,
            $this->supervisorReport,
        );

        $this->supervisorReport = '';
        $this->loadPage();
        session()->flash('success', 'Laporan Team berhasil dikirim ke Manager.');
    }

    public function render()
    {
        return view('livewire.page.main.work-management.division-project-team-detail');
    }

    public function getAutomaticProgressProperty(): int
    {
        $activeTasks = $this->tasks->reject(
            fn (Task $task) => $task->status === Task::STATUS_CANCELLED
        );

        if ($activeTasks->isEmpty()) {
            return 0;
        }

        return (int) round($activeTasks->avg(fn (Task $task) => (int) $task->progress));
    }

    private function loadPage(): void
    {
        $this->team->load('supervisor.user');

        $this->tasks = $this->divisionProject->tasks()
            ->where('team_id', $this->team->id)
            ->with(['assignee.user', 'reviews.reviewer.user'])
            ->orderByDesc('id')
            ->get();

        $this->latestSupervisorReport = $this->divisionProject->reports()
            ->where('report_level', ProjectReport::LEVEL_SUPERVISOR)
            ->where('team_id', $this->team->id)
            ->latest('id')
            ->first();
    }
}
