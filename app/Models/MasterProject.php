<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class MasterProject extends Model
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'created_by');
    }

    public function divisionProjects(): HasMany
    {
        return $this->hasMany(DivisionProject::class);
    }
}
