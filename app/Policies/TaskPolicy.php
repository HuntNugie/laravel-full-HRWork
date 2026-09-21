<?php

namespace App\Policies;

use App\Models\Employees;
use App\Models\Task;

class TaskPolicy
{
    public function viewAny(Employees $employee): bool
    {
        return $employee->user?->can('view-task') ?? false;
    }

    public function view(Employees $employee, Task $task): bool
    {
        return ($employee->user?->can('view-task') ?? false)
            && ((int) $task->assignee_id === (int) $employee->getKey()
                || (int) $task->created_by === (int) $employee->getKey()
                || (int) $task->team?->supervisor_id === (int) $employee->getKey());
    }

    public function create(Employees $employee): bool
    {
        return $employee->user?->can('create-task') ?? false;
    }

    public function update(Employees $employee, Task $task): bool
    {
        return ($employee->user?->can('update-task') ?? false)
            && ((int) $task->created_by === (int) $employee->getKey()
                || (int) $task->team?->supervisor_id === (int) $employee->getKey());
    }

    public function submit(Employees $employee, Task $task): bool
    {
        return ($employee->user?->can('submit-task') ?? false)
            && (int) $task->assignee_id === (int) $employee->getKey();
    }

    public function review(Employees $employee, Task $task): bool
    {
        return ($employee->user?->can('review-task') ?? false)
            && (int) $task->team?->supervisor_id === (int) $employee->getKey();
    }
}
