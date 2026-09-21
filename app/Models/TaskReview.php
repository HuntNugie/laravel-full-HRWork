<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class TaskReview extends Model
{
    public function task(){return $this->belongsTo(Task::class);}
    public function reviewer(){return $this->belongsTo(Employees::class, 'reviewer_id');}
}
