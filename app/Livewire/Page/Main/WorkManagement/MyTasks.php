<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\Employees;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'My Tasks'])]
class MyTasks extends Component
{
    public Collection $tasks;

    public function mount(): void
    {
        $this->authorize('viewAny', Task::class);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $query = Task::query()
            ->with(['divisionProject.masterProject', 'team', 'assignee.user'])
            ->latest();

        $this->applyScope($query, $employee);
        $this->tasks = $query->get();
    }

    public function render()
    {
        return view('livewire.page.main.work-management.my-tasks');
    }

    private function applyScope($query, Employees $employee): void
    {
        if (! $employee->user?->hasRole('task-worker')) {
            $query->whereKey(0);
            return;
        }

        $query->where('assignee_id', $employee->id);
    }

}
