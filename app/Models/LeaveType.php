<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded('id')]
class LeaveType extends Model
{
    public function contractLeaveEntitlement()
    {
        return $this->hasMany(ContractLeaveEntitlements::class, 'leave_type_id');
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }
}
