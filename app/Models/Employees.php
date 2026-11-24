<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
#[Guarded('id')]
class Employees extends Model implements HasMedia
{
    use InteractsWithMedia;
    public function registerMediaCollections(): void { $this->addMediaCollection('avatar')->singleFile(); }
    public function profile() { return $this->hasOne(Employee_profile::class, 'employee_id'); }
    public function position() { return $this->belongsTo(Position::class, 'position_id'); }
    public function employeeContract() { return $this->hasMany(EmployeeContract::class, 'employee_id'); }
    public function latestEmployeeContract() { return $this->hasOne(EmployeeContract::class, 'employee_id')->latestOfMany(); }
    public function activeContract() { return $this->hasOne(EmployeeContract::class, 'employee_id')->ofMany(['id' => 'max'], fn ($q) => $q->where('status', 'active')); }
    public function statusHistory() { return $this->hasMany(EmployeeStatusHistory::class, 'employee_id'); }
}
