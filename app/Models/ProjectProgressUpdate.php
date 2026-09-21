<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded('id')]
class ProjectProgressUpdate extends Model
{
    public function divisionProject(): BelongsTo
    {
        return $this->belongsTo(DivisionProject::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'reported_by');
    }
}
