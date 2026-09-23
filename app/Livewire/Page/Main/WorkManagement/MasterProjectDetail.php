<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Master Project'])]
class MasterProjectDetail extends Component
{
    public MasterProject $masterProject;

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('view', $masterProject);

        $employee = auth()->user()?->employees;
        $canSeeManagerData = $employee
            && (
                $employee->user?->hasRole('general-manager')
                || $masterProject->divisionProjects()->where('manager_id', $employee->id)->exists()
            );

        $relations = [
            'creator.user',
            'approver.user',
            'reviews.reviewer.user',
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.tasks',
        ];

        if ($canSeeManagerData) {
            $relations[] = 'divisionProjects.progressUpdates.reporter.user';
            $relations[] = 'divisionProjects.reports.reporter.user';
        }

        $this->masterProject = $masterProject->load($relations);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-detail');
    }
}
