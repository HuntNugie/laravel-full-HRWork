<?php

namespace App\Policies;

use App\Models\DivisionProject;
use App\Models\Employees;
use App\Models\Team;
use App\Models\User;

class DivisionProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-division-project') && $user->employees !== null;
    }

    public function view(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('view-division-project') || ! ($employee = $user->employees)) {
            return false;
        }

        if ($this->isGeneralManager($employee)) {
            return true;
        }

        if ((int) $divisionProject->manager_id === (int) $employee->id) {
            return true;
        }

        if ($divisionProject->teams()->where('supervisor_id', $employee->id)->exists()) {
            return true;
        }

        return $divisionProject->tasks()->where('assignee_id', $employee->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('create-division-project')
            && $this->isGeneralManager($user->employees);
    }

    public function update(User $user, DivisionProject $divisionProject): bool
    {
        return $user->can('update-division-project')
            && $this->isGeneralManager($user->employees);
    }

    public function assignTeam(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('assign-project-team') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || (int) $divisionProject->manager_id === (int) $employee->id;
    }

    public function submitForCompletion(User $user, DivisionProject $divisionProject): bool
    {
        return $user->can('submit-division-project-to-gm')
            && (int) $divisionProject->manager_id === (int) $user->employees?->id;
    }

    public function reviewCompletion(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('review-division-project') || ! ($employee = $user->employees)) {
            return false;
        }

        $masterProject = $divisionProject->masterProject;

        return $this->isGeneralManager($employee)
            && $masterProject !== null
            && (int) $masterProject->created_by === (int) $employee->id;
    }

    public function viewTeam(User $user, DivisionProject $divisionProject, Team $team): bool
    {
        if (! $user->can('view-division-project') || ! ($employee = $user->employees)) {
            return false;
        }

        if (! $divisionProject->teams()->whereKey($team->id)->exists()) {
            return false;
        }

        if ($this->isGeneralManager($employee)) {
            return true;
        }

        if ((int) $divisionProject->manager_id === (int) $employee->id) {
            return true;
        }

        return (int) $team->supervisor_id === (int) $employee->id;
    }

    private function isGeneralManager(?Employees $employee): bool
    {
        return $employee?->user?->hasRole('general-manager') ?? false;
    }
}
