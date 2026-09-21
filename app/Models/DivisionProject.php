<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class DivisionProject extends Model
{
    public function masterProject()
    {
        return $this->belongsTo(MasterProject::class);
    }

    public function division()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employees::class, 'manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'project_teams')->withPivot('assigned_by')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProjectProgressUpdate::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProjectReview::class);
    }

    public function audits()
    {
        return $this->morphMany(WorkManagementAudit::class, 'auditable');
    }

    public function automaticProgress(): int
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();
        $activeTasks = $tasks->reject(fn (Task $task) => $task->status === Task::STATUS_CANCELLED);

        if ($activeTasks->isEmpty()) {
            return 0;
        }

        return (int) round($activeTasks->avg(fn (Task $task) => (int) $task->progress));
    }

    public function allRequiredWorkCompleted(): bool
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();
        $activeTasks = $tasks->reject(fn (Task $task) => $task->status === Task::STATUS_CANCELLED);

        return $activeTasks->isNotEmpty()
            && $activeTasks->every(fn (Task $task) => $task->status === Task::STATUS_DONE);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'is_required' => 'boolean',
            'manual_progress' => 'integer',
        ];
    }
}
