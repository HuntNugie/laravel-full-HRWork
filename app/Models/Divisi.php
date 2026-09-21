<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Divisi extends Model
{
    // relasi ke manager
    public function manager()
    {
        return $this->belongsTo(Employees::class, 'manager_id');
    }

    // relasi ke team
    public function divisionProjects()
    {
        return $this->hasMany(DivisionProject::class, 'divisi_id');
    }

    // relasi ke team
    public function team()
    {
        return $this->hasMany(Team::class, 'divisi_id');
    }
}
