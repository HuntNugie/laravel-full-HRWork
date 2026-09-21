<?php

namespace App\Policies;

use App\Models\Employees;
use App\Models\MasterProject;

class MasterProjectPolicy
{
    public function viewAny(Employees $employee): bool
    {
        return $employee->user?->can('view-master-project') ?? false;
    }

    public function view(Employees $employee, MasterProject $masterProject): bool
    {
        return $employee->user?->can('view-master-project') ?? false;
    }

    public function create(Employees $employee): bool
    {
        return $employee->user?->can('create-master-project') ?? false;
    }

    public function update(Employees $employee, MasterProject $masterProject): bool
    {
        return $employee->user?->can('update-master-project') ?? false;
    }
}
