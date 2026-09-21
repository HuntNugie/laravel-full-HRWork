<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class DivisionProject extends Model
{
    public function masterProject(): BelongsTo
    {
        return $this->belongsTo(MasterProject::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'project_teams')->withPivot('assigned_by')->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProjectProgressUpdate::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class);
    }
}
