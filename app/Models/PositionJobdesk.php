<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class PositionJobdesk extends Model
{
    public function position(){
        return $this->belongsTo(Position::class,'position_id');
    }
}
