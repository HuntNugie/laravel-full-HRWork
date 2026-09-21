<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class MasterProject extends Model
{
    public function creator()
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(Employees::class, 'approved_by');
    }

    public function divisionProjects()
    {
        return $this->hasMany(DivisionProject::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, DivisionProject::class, 'master_project_id', 'division_project_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProjectReview::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }
}
