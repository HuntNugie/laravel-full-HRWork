<?php

namespace App\Models;

use Aliziodev\Wilayah\Models\Village;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfileAddress extends Model
{
    // relasi ke employe profile
    public function employee_profile(){
        $this->belongsTo(Employee_profile::class,'employee_profile_id');
    }

    public function village(){
        $this->belongsTo(Village::class,'village_code','code');
    }
}
