<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class team extends Model
{
    // relasi ke divisi
        public function divisi(){
            return $this->belongsTo(divisi::class,'divisi_id');
        }

    // relasi ke employees
        public function employees(){
            return $this->hasMany(employees::class,'team_id');
        }
    }
