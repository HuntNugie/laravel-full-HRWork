<?php

namespace Tests\Feature;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\WorkTime;
use App\Service\EmployeeDailyStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDailyStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-23 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_attendance_is_present_or_late_and_is_paid(): void
    {
        $employee = $this->createEmployeeContract('2026-09-01', null);

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-21',
            'check_in_at' => '2026-09-21 09:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-22',
            'check_in_at' => '2026-09-22 09:15:00',
            'status' => 'late',
            'late_minutes' => 15,
        ]);

        $statuses = app(EmployeeDailyStatusService::class)
            ->getStatuses($employee, '2026-09-21', '2026-09-22');

        $present = $statuses->firstWhere('date', '2026-09-21');
        $late = $statuses->firstWhere('date', '2026-09-22');

        $this->assertSame(EmployeeDailyStatusService::STATUS_PRESENT, $present['status']);
        $this->assertTrue($present['is_paid']);
        $this->assertFalse($present['is_late']);

        $this->assertSame(EmployeeDailyStatusService::STATUS_LATE, $late['status']);
        $this->assertTrue($late['is_paid']);
        $this->assertTrue($late['is_late']);
        $this->assertSame(15, $late['late_minutes']);
    }

    public function test_approved_leave_is_paid_and_not_unpresent(): void
    {
        $employee = $this->createEmployeeContract('2026-09-01', null);
        $contract = $employee->employeeContract()->firstOrFail();

        $this->createWorkingTime('selasa');

        $leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'default_days' => 12,
            'gender' => 'all',
            'status' => 'active',
        ]);

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-22',
            'end_date' => '2026-09-22',
            'total_days' => 1,
            'status' => 'approved',
        ]);

        $state = app(EmployeeDailyStatusService::class)
            ->getStatus($employee, '2026-09-22');

        $this->assertSame(EmployeeDailyStatusService::STATUS_PAID_LEAVE, $state['status']);
        $this->assertTrue($state['is_working_day']);
        $this->assertTrue($state['is_paid']);
        $this->assertFalse($state['is_unpresent']);
        $this->assertSame($contract->id, $state['contract_id']);
    }

    public function test_approved_sick_and_permit_are_unpaid_but_not_unpresent(): void
    {
        $employee = $this->createEmployeeContract('2026-09-01', null);

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-21',
            'type' => 'sakit',
            'reason' => 'Demam',
            'status' => 'approved',
        ]);

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-22',
            'type' => 'izin',
            'reason' => 'Keperluan keluarga',
            'status' => 'approved',
        ]);

        $statuses = app(EmployeeDailyStatusService::class)
            ->getStatuses($employee, '2026-09-21', '2026-09-22');

        $sick = $statuses->firstWhere('date', '2026-09-21');
        $permit = $statuses->firstWhere('date', '2026-09-22');

        $this->assertSame(EmployeeDailyStatusService::STATUS_ABSENCE_SICK, $sick['status']);
        $this->assertFalse($sick['is_paid']);
        $this->assertFalse($sick['is_unpresent']);

        $this->assertSame(EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT, $permit['status']);
        $this->assertFalse($permit['is_paid']);
        $this->assertFalse($permit['is_unpresent']);
    }

    public function test_past_unresolved_working_day_is_unpresent_and_current_or_future_day_is_pending(): void
    {
        $employee = $this->createEmployeeContract('2026-09-01', null);

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');
        $this->createWorkingTime('rabu');
        $this->createWorkingTime('kamis');

        $statuses = app(EmployeeDailyStatusService::class)
            ->getStatuses($employee, '2026-09-21', '2026-09-24');

        $unpresent = $statuses->firstWhere('date', '2026-09-21');
        $today = $statuses->firstWhere('date', '2026-09-23');
        $pending = $statuses->firstWhere('date', '2026-09-24');

        $this->assertSame(EmployeeDailyStatusService::STATUS_UNPRESENT, $unpresent['status']);
        $this->assertTrue($unpresent['is_unpresent']);
        $this->assertFalse($unpresent['is_paid']);

        $this->assertSame(EmployeeDailyStatusService::STATUS_PENDING, $today['status']);
        $this->assertFalse($today['is_unpresent']);

        $this->assertSame(EmployeeDailyStatusService::STATUS_PENDING, $pending['status']);
        $this->assertFalse($pending['is_unpresent']);
    }

    public function test_holiday_and_non_working_day_are_not_working_days(): void
    {
        $employee = $this->createEmployeeContract('2026-09-01', null);

        $this->createWorkingTime('selasa', true);
        $this->createWorkingTime('rabu', false);

        Holidays::create([
            'name' => 'Hari Libur',
            'date' => '2026-09-22',
        ]);

        $statuses = app(EmployeeDailyStatusService::class)
            ->getStatuses($employee, '2026-09-22', '2026-09-23');

        $holiday = $statuses->firstWhere('date', '2026-09-22');
        $nonWorking = $statuses->firstWhere('date', '2026-09-23');

        $this->assertSame(EmployeeDailyStatusService::STATUS_HOLIDAY, $holiday['status']);
        $this->assertFalse($holiday['is_working_day']);
        $this->assertFalse($holiday['is_paid']);
        $this->assertFalse($holiday['is_unpresent']);

        $this->assertSame(EmployeeDailyStatusService::STATUS_NON_WORKING, $nonWorking['status']);
        $this->assertFalse($nonWorking['is_working_day']);
        $this->assertFalse($nonWorking['is_paid']);
        $this->assertFalse($nonWorking['is_unpresent']);
    }

    public function test_date_outside_active_contract_is_outside_contract(): void
    {
        $employee = $this->createEmployeeContract('2026-09-15', '2026-09-30');

        $this->createWorkingTime('senin');

        $state = app(EmployeeDailyStatusService::class)
            ->getStatus($employee, '2026-09-14');

        $this->assertSame(EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT, $state['status']);
        $this->assertFalse($state['is_working_day']);
        $this->assertFalse($state['is_paid']);
        $this->assertFalse($state['is_unpresent']);
        $this->assertNull($state['contract_id']);
    }

    private function createEmployeeContract(string $startDate, ?string $endDate): Employees
    {
        $employee = Employees::create([
            'employee_code' => 'EMP-' . uniqid(),
            'user_id' => null,
            'team_id' => null,
            'position_id' => null,
        ]);

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-' . uniqid(),
            'employement_type' => 'pkwtt',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salary_daily' => 100000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return $employee;
    }

    private function createWorkingTime(string $dayOfWeek, bool $isWorkingDay = true): WorkTime
    {
        return WorkTime::create([
            'day_of_week' => $dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => $isWorkingDay,
        ]);
    }
}
