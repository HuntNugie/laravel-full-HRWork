<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class ProjectReport extends Model
{
    public const LEVEL_SUPERVISOR = 'supervisor';
    public const LEVEL_MANAGER = 'manager';

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function divisionProject()
    {
        return $this->belongsTo(DivisionProject::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Employees::class, 'reported_by');
    }

    protected function casts(): array
    {
        return [];
    }
}
