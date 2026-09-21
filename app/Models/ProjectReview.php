<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded('id')]
class ProjectReview extends Model
{
    public function divisionProject(): BelongsTo
    {
        return $this->belongsTo(DivisionProject::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'reviewer_id');
    }
}
