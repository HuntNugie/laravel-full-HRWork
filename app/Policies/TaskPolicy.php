<?php

namespace App\Policies;

use App\Models\Employees;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-task') && $user->employees !== null;
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->can('view-task') || ! ($employee = $user->employees)) {
            return false;
        }

        if ($this->isGeneralManager($employee)) {
            return true;
        }

        if ((int) $task->assignee_id === (int) $employee->id) {
            return true;
        }

        if ((int) $task->divisionProject?->manager_id === (int) $employee->id) {
            return true;
        }

        return (int) $task->team?->supervisor_id === (int) $employee->id;
    }

    public function create(User $user): bool
    {
        if (! $user->can('view-task') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || $employee->user?->hasRole('supervisor');
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->can('update-task') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || (int) $task->divisionProject?->manager_id === (int) $employee->id
            || (int) $task->team?->supervisor_id === (int) $employee->id;
    }

    public function updateOwn(User $user, Task $task): bool
    {
        return $user->can('update-own-task')
            && (int) $task->assignee_id === (int) $user->employees?->id;
    }

    public function submit(User $user, Task $task): bool
    {
        return $user->can('submit-task')
            && (int) $task->assignee_id === (int) $user->employees?->id;
    }

    public function review(User $user, Task $task): bool
    {
        return $user->can('review-task')
            && (int) $task->team?->supervisor_id === (int) $user->employees?->id;
    }

    public function cancel(User $user, Task $task): bool
    {
        if (! $user->can('update-task') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || (int) $task->divisionProject?->manager_id === (int) $employee->id
            || (int) $task->team?->supervisor_id === (int) $employee->id;
    }

    private function isGeneralManager(?Employees $employee): bool
    {
        return $employee?->user?->hasRole('general-manager') ?? false;
    }
}
