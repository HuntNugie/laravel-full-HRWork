<?php

namespace App\Policies;

use App\Models\Employees;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-task')
            && $user->employees !== null
            && ($user->employees?->user?->hasRole('task-worker') ?? false);
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->can('view-task') || ! ($employee = $user->employees)) {
            return false;
        }

        // A task detail is private to the employee who received the task.
        return $user->hasRole('task-worker')
            && (int) $task->assignee_id === (int) $employee->id;
    }

    public function create(User $user): bool
    {
        if (! $user->can('create-task') || ! ($employee = $user->employees)) {
            return false;
        }

        return $employee->user?->hasRole('supervisor') ?? false;
    }

    public function updateOwn(User $user, Task $task): bool
    {
        return $user->can('update-own-task')
            && (int) $task->assignee_id === (int) $user->employees?->id
            && ($user->employees?->user?->hasRole('task-worker') ?? false);
    }

    public function submit(User $user, Task $task): bool
    {
        return false;
    }

    public function review(User $user, Task $task): bool
    {
        return false;
    }

    public function update(User $user, Task $task): bool
    {
        return false;
    }

    public function cancel(User $user, Task $task): bool
    {
        if (! $user->can('update-task') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || $this->isProjectManager($task, $employee)
            || (int) $task->team?->supervisor_id === (int) $employee->id;
    }

    private function isProjectManager(Task $task, Employees $employee): bool
    {
        return (int) $task->divisionProject?->manager_id === (int) $employee->id;
    }

    private function isGeneralManager(?Employees $employee): bool
    {
        return $employee?->user?->hasRole('general-manager') ?? false;
    }
}
