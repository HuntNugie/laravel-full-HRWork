<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class Task extends Model
{
    public function divisionProject(): BelongsTo
    {
        return $this->belongsTo(DivisionProject::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TaskReview::class);
    }
}
