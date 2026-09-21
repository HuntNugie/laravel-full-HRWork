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

        $this->masterProject = $masterProject->load([
            'creator.user',
            'approver.user',
            'reviews.reviewer.user',
            'divisionProjects.division',
            'divisionProjects.manager.user',
            'divisionProjects.tasks',
        ]);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-project-detail');
    }
}
