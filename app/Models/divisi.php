<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class divisi extends Model
{

    // relasi ke team
    public function team(){
        return $this->hasMany(team::class,'divisi_id');
    }

}
