<?php

namespace Tests\Feature;

use App\Models\ContractLeaveEntitlements;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\WorkTime;
use App\Models\User;
use App\Service\LeaveRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class LeaveRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeEmployee(): array
    {
        $user = User::factory()->create();

        $employee = Employees::create([
            'EmployeeID' => 'EMP-' . uniqid(),
            'user_id' => $user->id,
        ]);

        $contract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_numnber' => 'CTR-' . uniqid(),
            'employement_type' => 'pkwt',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'salary_daily' => 100000,
            'status' => 'active',
        ]);

        $leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'default_days' => 12,
            'gender' => 'all',
            'status' => 'active',
        ]);

        $entitlement = ContractLeaveEntitlements::create([
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'days' => 12,
        ]);

        foreach ([
            'senin' => true,
            'selasa' => true,
            'rabu' => true,
            'kamis' => true,
            'jumat' => true,
            'sabtu' => false,
            'minggu' => false,
        ] as $day => $isWorkingDay) {
            WorkTime::create([
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_working_day' => $isWorkingDay,
            ]);
        }

        return [$user, $employee, $contract, $leaveType, $entitlement];
    }

    public function test_create_pending_binds_to_active_contract_and_entitlement(): void
    {
        [, $employee, $contract, $leaveType] = $this->makeEmployee();

        $request = app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-22',
            endDate: '2026-09-24',
            reason: 'Keperluan keluarga',
        );

        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'total_days' => 3,
            'status' => 'pending',
        ]);
    }

    public function test_create_pending_counts_only_scheduled_workdays_and_excludes_holidays(): void
    {
        [, $employee, $contract, $leaveType] = $this->makeEmployee();

        Holidays::create([
            'name' => 'Libur Nasional',
            'description' => 'Hari libur yang terdaftar',
            'date' => '2026-09-23',
        ]);

        $request = app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-22',
            endDate: '2026-09-27',
            reason: 'Keperluan keluarga',
        );

        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'employee_contract_id' => $contract->id,
            'start_date' => '2026-09-22',
            'end_date' => '2026-09-27',
            'total_days' => 3,
            'status' => 'pending',
        ]);
    }

    public function test_create_pending_rejects_range_without_scheduled_workday(): void
    {
        [, $employee, , $leaveType] = $this->makeEmployee();

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-26',
            endDate: '2026-09-27',
            reason: 'Keperluan keluarga',
        );
    }

    public function test_create_pending_rejects_period_outside_contract(): void
    {
        [, $employee, , $leaveType] = $this->makeEmployee();

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2027-01-02',
            endDate: '2027-01-03',
            reason: 'Keperluan keluarga',
        );
    }

    public function test_leave_balance_is_scoped_to_contract(): void
    {
        [, $employee, $newContract, $leaveType, $newEntitlement] = $this->makeEmployee();

        $oldContract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_numnber' => 'CTR-' . uniqid(),
            'employement_type' => 'pkwt',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'salary_daily' => 90000,
            'status' => 'expired',
        ]);

        $oldEntitlement = ContractLeaveEntitlements::create([
            'employee_contract_id' => $oldContract->id,
            'leave_type_id' => $leaveType->id,
            'days' => 12,
        ]);

        $oldRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $oldContract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2025-06-02',
            'end_date' => '2025-06-04',
            'total_days' => 3,
            'reason' => 'Cuti lama',
            'status' => 'approved',
        ]);

        $this->assertNotSame($oldContract->id, $newContract->id);
        $this->assertSame(3, app(LeaveRequestService::class)->usedDays($oldEntitlement, 2025));
        $this->assertSame(0, app(LeaveRequestService::class)->usedDays($newEntitlement, 2026));
        $this->assertSame(12, app(LeaveRequestService::class)->remainingDays($newEntitlement, 2026));
        $this->assertDatabaseHas('leave_requests', ['id' => $oldRequest->id]);
    }

    public function test_create_pending_rejects_active_leave_overlap(): void
    {
        [, $employee, $contract, $leaveType] = $this->makeEmployee();

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-22',
            'end_date' => '2026-09-24',
            'total_days' => 3,
            'reason' => 'Pengajuan pertama',
            'status' => 'pending',
        ]);

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-24',
            endDate: '2026-09-25',
            reason: 'Pengajuan kedua',
        );
    }

    public function test_create_pending_rejects_absence_overlap(): void
    {
        [, $employee, , $leaveType] = $this->makeEmployee();

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-23',
            'type' => 'izin',
            'reason' => 'Keperluan pribadi',
            'status' => 'pending',
        ]);

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-23',
            endDate: '2026-09-23',
            reason: 'Cuti',
        );
    }

    public function test_create_pending_rejects_attendance_overlap(): void
    {
        [, $employee, , $leaveType] = $this->makeEmployee();

        \App\Models\Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-24',
            'check_in_at' => '2026-09-24 08:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->createPending(
            employee: $employee,
            leaveTypeId: $leaveType->id,
            startDate: '2026-09-24',
            endDate: '2026-09-24',
            reason: 'Cuti',
        );
    }

    public function test_approve_revalidates_attendance_and_uses_contract_entitlement(): void
    {
        [$approver, $employee, $contract, $leaveType] = $this->makeEmployee();

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-25',
            'end_date' => '2026-09-26',
            'total_days' => 2,
            'reason' => 'Acara keluarga',
            'status' => 'pending',
        ]);

        App\Models\Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-25',
            'check_in_at' => '2026-09-25 08:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $this->expectException(LogicException::class);

        app(LeaveRequestService::class)->approve(
            request: $request,
            approvedBy: $approver->id,
        );

        $this->assertDatabaseHas('leave_requests', [
            'id' => $request->id,
            'status' => 'pending',
        ]);
    }

    public function test_reject_and_cancel_are_terminal_safe_workflows(): void
    {
        [$approver, $employee, $contract, $leaveType] = $this->makeEmployee();
        $service = app(LeaveRequestService::class);

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-28',
            'end_date' => '2026-09-28',
            'total_days' => 1,
            'reason' => 'Acara keluarga',
            'status' => 'pending',
        ]);

        $rejected = $service->reject(
            request: $request,
            rejectedBy: $approver->id,
            reason: 'Jadwal kerja tidak memungkinkan.',
        );

        $this->assertSame('rejected', $rejected->status);
        $this->assertNotNull($rejected->rejected_at);

        $cancelRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-29',
            'end_date' => '2026-09-29',
            'total_days' => 1,
            'reason' => 'Acara keluarga',
            'status' => 'pending',
        ]);

        $cancelled = $service->cancel($cancelRequest);

        $this->assertSame('cancelled', $cancelled->status);
        $this->assertNotNull($cancelled->cancelled_at);

        $this->expectException(LogicException::class);
        $service->cancel($cancelled);
    }
}
