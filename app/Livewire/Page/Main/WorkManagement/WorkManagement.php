<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Work Management'])]
class WorkManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $projects = MasterProject::query()
            ->with(['creator', 'divisionProjects.division'])
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.page.main.work-management.work-management', compact('projects'));
    }
}
