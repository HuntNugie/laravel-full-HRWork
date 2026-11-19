<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Team extends Model
{
    // relasi ke divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    // relasi ke employees
    public function employees()
    {
        return $this->hasMany(Employees::class, 'team_id');
    }

    public function nonSupervisors()
    {
        return $this->hasMany(Employees::class, 'team_id')
            ->where('id', '!=', $this->supervisor_id);
    }

    public function supervisor()
    {
        return $this->belongsTo(Employees::class, 'supervisor_id');
    }
}
