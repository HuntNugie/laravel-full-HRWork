<?php

namespace App\Policies;

use App\Models\DivisionProject;
use App\Models\Employees;
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
        return $user->can('create-division-project') && $this->isGeneralManager($user->employees);
    }

    public function update(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('update-division-project') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || (int) $divisionProject->manager_id === (int) $employee->id;
    }

    public function assignTeam(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('assign-project-team') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            || (int) $divisionProject->manager_id === (int) $employee->id;
    }

    public function reportProgress(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('report-project-progress') || ! ($employee = $user->employees)) {
            return false;
        }

        if ((int) $divisionProject->manager_id === (int) $employee->id) {
            return true;
        }

        return $divisionProject->teams()->where('supervisor_id', $employee->id)->exists();
    }

    public function review(User $user, DivisionProject $divisionProject): bool
    {
        return $user->can('review-division-project')
            && (int) $divisionProject->manager_id === (int) $user->employees?->id;
    }

    public function submitSupervisorReport(User $user, DivisionProject $divisionProject): bool
    {
        if (! $user->can('submit-division-project-report') || ! ($employee = $user->employees)) {
            return false;
        }

        return $divisionProject->teams()
            ->where('supervisor_id', $employee->id)
            ->exists();
    }

    public function submitToGM(User $user, DivisionProject $divisionProject): bool
    {
        return $user->can('submit-division-project-to-gm')
            && (int) $divisionProject->manager_id === (int) $user->employees?->id;
    }

    private function isGeneralManager(?Employees $employee): bool
    {
        return $employee?->user?->hasRole('general-manager') ?? false;
    }
}
