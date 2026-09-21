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

    public int $progress = 0;
    public string $result = '';
    public string $blockedReason = '';
    public string $workStatus = Task::STATUS_IN_PROGRESS;
    public ?string $reviewDecision = null;
    public string $reviewFeedback = '';

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);
        $this->loadTask($task);
    }

    public function saveWork(WorkManagementService $service): void
    {
        $this->authorize('updateOwn', $this->task);

        $this->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'result' => ['nullable', 'string'],
            'blockedReason' => ['nullable', 'string'],
            'workStatus' => ['required', 'in:in_progress,blocked'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->updateTaskWork(
            $this->task,
            $employee,
            $this->progress,
            $this->result ?: null,
            $this->blockedReason ?: null,
            $this->workStatus,
        );

        $this->loadTask($this->task);
        session()->flash('success', 'Pekerjaan task berhasil diperbarui.');
    }

    public function submit(WorkManagementService $service): void
    {
        $this->authorize('submit', $this->task);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->submitTask($this->task, $employee);
        $this->loadTask($this->task);
        session()->flash('success', 'Task berhasil disubmit untuk review.');
    }

    public function review(WorkManagementService $service): void
    {
        $this->authorize('review', $this->task);

        $this->validate([
            'reviewDecision' => ['required', 'in:approved,rejected'],
            'reviewFeedback' => ['nullable', 'string'],
        ]);

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $service->reviewTask(
            $this->task,
            $employee,
            $this->reviewDecision,
            $this->reviewFeedback ?: null,
        );

        $this->reviewDecision = null;
        $this->reviewFeedback = '';
        $this->loadTask($this->task);
        session()->flash('success', 'Review task berhasil disimpan.');
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
            'reviews.reviewer.user',
        ]);

        $this->progress = (int) $this->task->progress;
        $this->result = (string) ($this->task->result ?? '');
        $this->blockedReason = (string) ($this->task->blocked_reason ?? '');
        $this->workStatus = $this->task->status === Task::STATUS_BLOCKED
            ? Task::STATUS_BLOCKED
            : Task::STATUS_IN_PROGRESS;
    }
}
