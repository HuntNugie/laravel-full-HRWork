<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class Employees extends Model
{
    // relasi ke users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // relasi ke profile
    public function profile()
    {
        return $this->hasOne(Employee_profile::class, 'employee_id');
    }

    // relasi ke team
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // relasi ke position
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // relasi ke employee contract
    public function employeeContract()
    {
        return $this->hasMany(EmployeeContract::class, 'employee_id');
    }

    // relasi has one untuk contract employee terbaru
    public function latestEmployeeContract()
    {
        return $this->hasOne(EmployeeContract::class, 'employee_id')->latestOfMany();
    }

    // relasi ke status history
    public function statusHistory()
    {
        return $this->hasMany(EmployeeStatusHistory::class, 'employee_id');
    }

    // relasi supervisor team
    public function supervisorTeam()
    {
        return $this->hasOne(Team::class, 'supervisor_id');
    }
}
