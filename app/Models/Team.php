<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Team extends Model
{
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function employees()
    {
        return $this->hasMany(Employees::class, 'team_id');
    }

    public function nonSupervisors()
    {
        return $this->hasMany(Employees::class, 'team_id')
            ->where('id', '!=', $this->supervisor_id);
    }

    public function supervisor()
    {
        return $this->belongsTo(Employees::class, 'supervisor_id');
    }

    public function divisionProjects()
    {
        return $this->belongsToMany(DivisionProject::class, 'project_teams')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'team_id');
    }
}