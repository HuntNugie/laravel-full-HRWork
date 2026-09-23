<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Final Approval Master Project'])]
class MasterProjectApprove extends Component
{
    public MasterProject $masterProject;
    public ?string $decision = null;
    public string $feedback = '';
    public array $managerReviewFeedback = [];

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('view', $masterProject);
        $this->authorize('approve', $masterProject);
        $this->masterProject = $masterProject->load([
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.progressUpdates.reporter.user',
            'divisionProjects.reports.reporter.user',
            'divisionProjects.reviews.reviewer.user',
            'divisionProjects.tasks',
        ]);
    }

    public function reviewManagerReport(int $divisionProjectId, string $decision, WorkManagementService $service): void
    {
        $project = $this->masterProject->divisionProjects->firstWhere('id', $divisionProjectId);

        if (! $project) {
            abort(404);
        }

        $this->authorize('reviewManagerReport', $project);

        $feedback = (string) ($this->managerReviewFeedback[$divisionProjectId] ?? '');

        if ($decision === 'rejected' && blank(trim($feedback))) {
            $this->addError("managerReviewFeedback.$divisionProjectId", 'Feedback wajib diisi ketika laporan dikembalikan.');
            return;
        }

        if (! in_array($decision, ['approved', 'rejected'], true)) {
            $this->addError("managerReviewFeedback.$divisionProjectId", 'Decision review tidak valid.');
            return;
        }

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->reviewManagerReport(
            $project,
            $employee,
            $decision,
            $feedback ?: null,
        );

        $this->loadMasterProject();

        session()->flash(
            'success',
            $decision === 'approved'
                ? 'Progress dan laporan Manager berhasil di-approve.'
                : 'Progress dan laporan Manager dikembalikan untuk revisi.'
        );
    }

    public function approve(WorkManagementService $service): void
    {
        $this->authorize('approve', $this->masterProject);

        $this->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'feedback' => ['nullable', 'string'],
        ]);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->reviewMasterProject(
            $this->masterProject,
            $employee,
            $this->decision,
            $this->feedback ?: null,
        );

        session()->flash('success', 'Final approval Master Project berhasil disimpan.');
        $this->redirectRoute('work-management.master-projects.show', ['masterProject' => $this->masterProject], navigate: true);
    }

    private function loadMasterProject(): void
    {
        $this->masterProject = $this->masterProject->refresh()->load([
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.progressUpdates.reporter.user',
            'divisionProjects.reports.reporter.user',
            'divisionProjects.reviews.reviewer.user',
            'divisionProjects.tasks',
        ]);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-approve');
    }
}
