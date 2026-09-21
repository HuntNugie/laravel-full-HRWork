<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class ProjectReview extends Model
{
    public function divisionProject(){return $this->belongsTo(DivisionProject::class);}
    public function masterProject(){return $this->belongsTo(MasterProject::class);}
    public function reviewer(){return $this->belongsTo(Employees::class, 'reviewer_id');}
}
