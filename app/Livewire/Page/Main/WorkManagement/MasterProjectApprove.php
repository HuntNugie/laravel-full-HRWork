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

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('view', $masterProject);
        $this->authorize('approve', $masterProject);
        $this->masterProject = $masterProject->load([
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.progressUpdates.reporter.user',
            'divisionProjects.reports.reporter.user',
            'divisionProjects.tasks',
        ]);
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

        session()->flash('success', 'Final approval berhasil disimpan.');
        $this->redirectRoute('work-management.master-projects.show', ['masterProject' => $this->masterProject], navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-approve');
    }
}
