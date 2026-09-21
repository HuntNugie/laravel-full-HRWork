<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
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
        $this->projects = MasterProject::query()
            ->with('creator')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.page.main.work-management.master-projects');
    }
}
