<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\Task;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Task'])]
class TaskDetail extends Component
{
    public Task $task;

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);
        $this->loadTask($task);
    }

    public function toggleCompletion(
        bool $completed,
        WorkManagementService $service,
    ): void {
        $this->authorize('updateOwn', $this->task);

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $service->toggleTaskCompletion(
            $this->task,
            $employee,
            $completed,
        );

        $this->loadTask($this->task);

        session()->flash(
            'success',
            $completed
                ? 'Task ditandai selesai.'
                : 'Task dikembalikan ke daftar pekerjaan.'
        );
    }

    public function render()
    {
        return view('livewire.page.main.work-management.task-detail');
    }

    private function loadTask(Task $task): void
    {
        $this->task = $task->refresh()->load([
            'divisionProject.masterProject',
            'divisionProject.manager.user',
            'team.supervisor.user',
            'assignee.user',
            'creator.user',
        ]);
    }
}
