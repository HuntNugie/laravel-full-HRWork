<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    // relasi ke divisi
        public function divisi(){
            return $this->belongsTo(Divisi::class,'divisi_id');
        }

    // relasi ke employees
        public function employees(){
            return $this->hasMany(Employees::class,'team_id');
        }
    }
