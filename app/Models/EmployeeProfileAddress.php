<?php

namespace App\Models;

use Aliziodev\Wilayah\Models\Village;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class EmployeeProfileAddress extends Model
{
    // relasi ke employe profile
    public function employee_profile()
    {
        return $this->belongsTo(Employee_profile::class, 'employee_profile_id');
    }

    public function village()
    {
        return  $this->belongsTo(Village::class, 'village_code', 'code');
    }
}
