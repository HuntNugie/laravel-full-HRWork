<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\Team;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Division Project'])]
class DivisionProjectDetail extends Component
{
    public DivisionProject $divisionProject;
    public Collection $availableTeams;

    public ?int $team_id = null;
    public int $manualProgress = 0;
    public string $progressNote = '';
    public ?string $reviewDecision = null;
    public string $reviewFeedback = '';
    public string $supervisorReport = '';
    public string $managerReport = '';

    public function mount(DivisionProject $divisionProject): void
    {
        $this->authorize('view', $divisionProject);
        $this->loadProject($divisionProject);
    }

    public function assignTeam(WorkManagementService $service): void
    {
        $this->authorize('assignTeam', $this->divisionProject);
        $this->validate(['team_id' => ['required', 'integer', 'exists:teams,id']]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->assignTeam(
            $this->divisionProject,
            Team::findOrFail($this->team_id),
            $employee,
        );

        $this->team_id = null;
        $this->loadProject($this->divisionProject);
        session()->flash('success', 'Team berhasil ditugaskan.');
    }

    public function reportProgress(WorkManagementService $service): void
    {
        $this->authorize('reportProgress', $this->divisionProject);
        $this->validate([
            'manualProgress' => ['required', 'integer', 'min:0', 'max:100'],
            'progressNote' => ['nullable', 'string'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->reportManualProgress(
            $this->divisionProject,
            $employee,
            $this->manualProgress,
            $this->progressNote ?: null,
        );

        $this->progressNote = '';
        $this->loadProject($this->divisionProject);
        session()->flash('success', 'Progress manual berhasil dicatat.');
    }

    public function submitSupervisorReport(WorkManagementService $service): void
    {
        $this->authorize('submitSupervisorReport', $this->divisionProject);

        $this->validate([
            'supervisorReport' => ['required', 'string'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->submitSupervisorReport(
            $this->divisionProject,
            $employee,
            $this->supervisorReport,
        );

        $this->supervisorReport = '';
        $this->loadProject($this->divisionProject);
        session()->flash('success', 'Laporan Supervisor berhasil dikirim ke Manager.');
    }

    public function submitToGM(WorkManagementService $service): void
    {
        $this->authorize('submitToGM', $this->divisionProject);

        $this->validate([
            'managerReport' => ['required', 'string'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->submitDivisionProjectToGM(
            $this->divisionProject,
            $employee,
            $this->managerReport,
        );

        $this->managerReport = '';
        $this->loadProject($this->divisionProject);
        session()->flash('success', 'Laporan Manager berhasil diteruskan ke General Manager.');
    }

    public function render()
    {
        return view('livewire.page.main.work-management.division-project-detail');
    }

    private function loadProject(DivisionProject $divisionProject): void
    {
        $employee = Auth::user()?->employees;
        $canSeeManagerData = $employee
            && (
                (int) $divisionProject->manager_id === (int) $employee->id
                || $employee->user?->hasRole('general-manager')
            );

        $relations = [
            'masterProject.reviews.reviewer.user',
            'division',
            'manager.user',
            'teams.supervisor.user',
            'tasks.team',
            'tasks.assignee.user',
            'tasks.reviews.reviewer.user',
            'reviews.reviewer.user',
        ];

        if ($canSeeManagerData) {
            $relations[] = 'progressUpdates.reporter.user';
            $relations[] = 'reports.team';
            $relations[] = 'reports.reporter.user';
        }

        $this->divisionProject = $divisionProject->refresh()->load($relations);

        $this->manualProgress = $canSeeManagerData
            ? (int) $this->divisionProject->manual_progress
            : 0;

        $latestManagerReport = $canSeeManagerData
            ? $this->divisionProject->reports
                ->where('report_level', \App\Models\ProjectReport::LEVEL_MANAGER)
                ->sortByDesc('id')
                ->first()
            : null;

        $this->managerReport = $latestManagerReport?->status === \App\Models\ProjectReport::STATUS_REJECTED
            ? (string) $latestManagerReport->content
            : '';

        $assignedIds = $this->divisionProject->teams->pluck('id');

        $this->availableTeams = Team::query()
            ->where('divisi_id', $this->divisionProject->divisi_id)
            ->where('is_active', 'active')
            ->whereNotNull('supervisor_id')
            ->whereNotIn('id', $assignedIds)
            ->with('supervisor.user')
            ->orderBy('name')
            ->get();
    }
}
