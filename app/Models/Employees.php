<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
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

    // relasi ke divisi yang dipimpin
    public function managedDivisi()
    {
        return $this->hasOne(Divisi::class, 'manager_id');
    }

    // scope untuk kandidat manager divisi yang masih available
    public function scopeAvailableDivisionManagers(Builder $query, ?int $exceptDivisionId = null): Builder
    {
        return $query
            ->where('status_employee', 'active')
            ->whereNull('team_id')
            ->whereDoesntHave('supervisorTeam')
            ->where(function (Builder $query) use ($exceptDivisionId) {
                $query->whereDoesntHave('managedDivisi');

                if ($exceptDivisionId) {
                    $query->orWhereHas('managedDivisi', function (Builder $query) use ($exceptDivisionId) {
                        $query->whereKey($exceptDivisionId);
                    });
                }
            });
    }

    // relasi supervisor team
    public function supervisorTeam()
    {
        return $this->hasOne(Team::class, 'supervisor_id');
    }

    // relasi ke attendances
    public function attendances()
    {
        return $this->hasMany(Attendances::class, "employee_id");
    }

    // relasi ke employee_absence_request untuk sakit atau izin
    public function employeeAbsenceRequest()
    {
        return $this->hasMany(EmployeeAbsenceRequest::class, "employee_id");
    }

    // relasi ke leaveRequest
    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function resignations()
    {
        return $this->hasMany(EmployeeResignation::class, 'employee_id');
    }

    public function terminations()
    {
        return $this->hasMany(EmployeeTermination::class, 'employee_id');
    }

    protected function casts(): array
    {
        return [
            'JoinDate' => 'date',
            'ResignDate' => 'date',
            'TerminationDate' => 'date',
        ];
    }
}
