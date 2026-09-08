<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded("id")]
class EmployeeAbsenceRequest extends Model
{
    // relasi kepada employee yang sakit
    public function employees()
    {
        return $this->belongsTo(Employees::class, "employee_id");
    }

    // relasi kepada user yang approve
    public function approver()
    {
        return $this->belongsTo(User::class, "approved_by");
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
