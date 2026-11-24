<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
#[Guarded('id')]
class EmployeeContract extends Model
{
    public function employees() { return $this->belongsTo(Employees::class, 'employee_id'); }
    public function position() { return $this->belongsTo(Position::class, 'position_id'); }
    public function benefits() { return $this->belongsToMany(Benefit::class, 'contract_benefits', 'employee_contract_id', 'benefit_id')->withPivot('amount', 'name_snapshot', 'description_snapshot')->withTimestamps(); }
    public function contractLeave() { return $this->hasMany(ContractLeaveEntitlements::class, 'employee_contract_id'); }
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date', 'ended_at' => 'date']; }
}
