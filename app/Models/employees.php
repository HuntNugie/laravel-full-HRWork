<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
#[Guarded('id')]
class employees extends Model
{
    // relasi ke users
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    // relasi ke profile
    public function profile(){
        return $this->hasOne(employee_profile::class,'employee_id');
    }

    // relasi ke team
    public function team(){
        return $this->belongsTo(team::class,'team_id');
    }

    // relasi ke position
    public function position(){
        return $this->belongsTo(position::class,'position_id');
    }
}
