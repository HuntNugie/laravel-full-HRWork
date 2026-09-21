<?php

namespace App\Policies;

use App\Models\DivisionProject;
use App\Models\Employees;

class DivisionProjectPolicy
{
    public function viewAny(Employees $employee): bool
    {
        return $employee->user?->can('view-division-project') ?? false;
    }

    public function view(Employees $employee, DivisionProject $divisionProject): bool
    {
        return $employee->user?->can('view-division-project') ?? false;
    }

    public function create(Employees $employee): bool
    {
        return $employee->user?->can('create-division-project') ?? false;
    }

    public function update(Employees $employee, DivisionProject $divisionProject): bool
    {
        return $employee->user?->can('update-division-project') ?? false;
    }
}
