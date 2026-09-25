<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\ProjectReview;
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
    public ?string $reviewFeedback = null;

    public function mount(DivisionProject $divisionProject): void
    {
        $this->authorize('view', $divisionProject);
        $this->loadProject($divisionProject);
    }

    public function assignTeam(WorkManagementService $service): void
    {
        $this->authorize('assignTeam', $this->divisionProject);

        $this->validate([
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ]);

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

    public function submitForCompletion(WorkManagementService $service): void
    {
        $this->authorize('submitForCompletion', $this->divisionProject);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->submitDivisionProjectForCompletion(
            $this->divisionProject,
            $employee,
        );

        $this->reviewFeedback = null;
        $this->loadProject($this->divisionProject);

        session()->flash('success', 'Division Project berhasil diajukan untuk approval GM.');
    }

    public function approveCompletion(WorkManagementService $service): void
    {
        $this->authorize('reviewCompletion', $this->divisionProject);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->reviewDivisionProjectCompletion(
            $this->divisionProject,
            $employee,
            'approved',
        );

        $this->loadProject($this->divisionProject);

        session()->flash('success', 'Division Project ditandai complete.');
    }

    public function requestRevision(WorkManagementService $service): void
    {
        $this->authorize('reviewCompletion', $this->divisionProject);

        $this->validate([
            'reviewFeedback' => ['required', 'string', 'max:5000'],
        ]);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->reviewDivisionProjectCompletion(
            $this->divisionProject,
            $employee,
            'rejected',
            $this->reviewFeedback,
        );

        $this->reviewFeedback = null;
        $this->loadProject($this->divisionProject);

        session()->flash('success', 'Division Project dikembalikan untuk revisi.');
    }

    public function render()
    {
        return view('livewire.page.main.work-management.division-project-detail');
    }

    private function loadProject(DivisionProject $divisionProject): void
    {
        $this->divisionProject = $divisionProject->refresh()->load([
            'masterProject',
            'division',
            'manager.user',
            'teams.supervisor.user',
            'teams.employees.user',
            'tasks.team',
            'tasks.assignee.user',
        ]);

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
