<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Finalisasi Master Project'])]
class MasterProjectApprove extends Component
{
    public MasterProject $masterProject;

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('view', $masterProject);
        $this->authorize('approve', $masterProject);

        $this->masterProject = $masterProject->load([
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.teams',
            'divisionProjects.tasks',
        ]);
    }

    public function complete(WorkManagementService $service): void
    {
        $this->authorize('approve', $this->masterProject);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->completeMasterProject(
            $this->masterProject,
            $employee,
        );

        session()->flash('success', 'Master Project berhasil ditandai selesai.');
        $this->masterProject = $this->masterProject->refresh()->load([
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.teams',
            'divisionProjects.tasks',
        ]);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-approve');
    }
}
