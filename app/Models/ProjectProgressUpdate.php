<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class ProjectProgressUpdate extends Model
{
    public function divisionProject(){return $this->belongsTo(DivisionProject::class);}
    public function reporter(){return $this->belongsTo(Employees::class, 'reported_by');}
}
