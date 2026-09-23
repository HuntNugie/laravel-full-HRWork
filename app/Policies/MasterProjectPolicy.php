<?php

namespace App\Policies;

use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\User;

class MasterProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-master-project') && $user->employees !== null;
    }

    public function view(User $user, MasterProject $masterProject): bool
    {
        if (! $user->can('view-master-project') || ! ($employee = $user->employees)) {
            return false;
        }

        if ($this->isGeneralManager($employee)) {
            return true;
        }

        if ($masterProject->divisionProjects()->where('manager_id', $employee->id)->exists()) {
            return true;
        }

        if ($masterProject->divisionProjects()
            ->whereHas('teams', fn ($query) => $query->where('supervisor_id', $employee->id))
            ->exists()) {
            return true;
        }

        return $masterProject->tasks()->where('assignee_id', $employee->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('create-master-project') && $this->isGeneralManager($user->employees);
    }

    public function update(User $user, MasterProject $masterProject): bool
    {
        return $user->can('update-master-project') && $this->isGeneralManager($user->employees);
    }

    public function approve(User $user, MasterProject $masterProject): bool
    {
        if (! $user->can('approve-master-project') || ! ($employee = $user->employees)) {
            return false;
        }

        return $this->isGeneralManager($employee)
            && (int) $masterProject->created_by === (int) $employee->id;
    }

    private function isGeneralManager(?Employees $employee): bool
    {
        return $employee?->user?->hasRole('general-manager') ?? false;
    }
}
