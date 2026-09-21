<?php

namespace Tests\Feature;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\ContractLeaveEntitlements;
use App\Models\User;
use App\Service\AbsenceRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AbsenceRequestServiceTest extends TestCase
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

        return [$user, $employee, $contract];
    }

    public function test_create_pending_absence_request(): void
    {
        [, $employee] = $this->makeEmployee();

        $request = app(AbsenceRequestService::class)->create(
            employee: $employee,
            type: 'sakit',
            reason: 'Demam',
            date: '2026-09-22',
        );

        $this->assertDatabaseHas('employee_absence_requests', [
            'id' => $request->id,
            'employee_id' => $employee->id,
            'type' => 'sakit',
            'date' => '2026-09-22',
            'status' => 'pending',
        ]);
    }

    public function test_create_rejects_duplicate_date(): void
    {
        [, $employee] = $this->makeEmployee();
        $service = app(AbsenceRequestService::class);

        $service->create(
            employee: $employee,
            type: 'izin',
            reason: 'Keperluan pribadi',
            date: '2026-09-22',
        );

        $this->expectException(LogicException::class);

        $service->create(
            employee: $employee,
            type: 'sakit',
            reason: 'Demam',
            date: '2026-09-22',
        );
    }

    public function test_create_rejects_after_attendance(): void
    {
        [, $employee] = $this->makeEmployee();

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-23',
            'check_in_at' => '2026-09-23 08:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $this->expectException(LogicException::class);

        app(AbsenceRequestService::class)->create(
            employee: $employee,
            type: 'izin',
            reason: 'Keperluan pribadi',
            date: '2026-09-23',
        );
    }

    public function test_create_rejects_when_active_leave_exists(): void
    {
        [, $employee, $contract] = $this->makeEmployee();

        $leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'default_days' => 12,
            'gender' => 'all',
            'status' => 'active',
        ]);

        ContractLeaveEntitlements::create([
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'days' => 12,
        ]);

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-24',
            'end_date' => '2026-09-24',
            'total_days' => 1,
            'reason' => 'Cuti',
            'status' => 'pending',
        ]);

        $this->expectException(LogicException::class);

        app(AbsenceRequestService::class)->create(
            employee: $employee,
            type: 'izin',
            reason: 'Keperluan pribadi',
            date: '2026-09-24',
        );
    }

    public function test_any_existing_absence_request_blocks_check_in(): void
    {
        [, $employee] = $this->makeEmployee();

        $request = EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-25',
            'type' => 'izin',
            'reason' => 'Keperluan pribadi',
            'status' => 'rejected',
        ]);

        $this->assertNotNull($request->id);

        $this->expectException(LogicException::class);

        app(AbsenceRequestService::class)->ensureNoRequestForCheckIn(
            employee: $employee,
            date: '2026-09-25',
        );
    }

    public function test_approve_reject_and_stale_approve_are_safe(): void
    {
        [$approver, $employee] = $this->makeEmployee();
        $service = app(AbsenceRequestService::class);

        $request = $service->create(
            employee: $employee,
            type: 'sakit',
            reason: 'Demam',
            date: '2026-09-26',
        );

        $approved = $service->approve(
            request: $request,
            approvedBy: $approver->id,
        );

        $this->assertSame('approved', $approved->status);
        $this->assertSame($approver->id, $approved->approved_by);
        $this->assertNotNull($approved->approved_at);

        $this->expectException(LogicException::class);
        $service->approve(
            request: $approved,
            approvedBy: $approver->id,
        );
    }

    public function test_reject_changes_pending_request_to_rejected(): void
    {
        [$approver, $employee] = $this->makeEmployee();
        $request = app(AbsenceRequestService::class)->create(
            employee: $employee,
            type: 'izin',
            reason: 'Keperluan keluarga',
            date: '2026-09-27',
        );

        $rejected = app(AbsenceRequestService::class)->reject(
            request: $request,
            rejectedBy: $approver->id,
        );

        $this->assertSame('rejected', $rejected->status);
        $this->assertSame($approver->id, $rejected->approved_by);
        $this->assertNotNull($rejected->approved_at);
    }
}
