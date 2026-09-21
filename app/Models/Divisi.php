<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded('id')]
class Divisi extends Model
{
    public function manager()
    {
        return $this->belongsTo(Employees::class, 'manager_id');
    }

    public function team()
    {
        return $this->hasMany(Team::class, 'divisi_id');
    }

    public function divisionProjects(): HasMany
    {
        return $this->hasMany(DivisionProject::class, 'divisi_id');
    }
}
