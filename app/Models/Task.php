<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Task extends Model
{
    public const STATUS_TO_DO = 'to_do';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    public function divisionProject()
    {
        return $this->belongsTo(DivisionProject::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function assignee()
    {
        return $this->belongsTo(Employees::class, 'assignee_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(TaskReview::class);
    }

    public function audits()
    {
        return $this->morphMany(WorkManagementAudit::class, 'auditable');
    }

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'due_date' => 'date',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
