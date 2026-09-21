<?php

namespace Tests\Feature;

use App\Models\AttedanceSetting;
use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\WorkTime;
use App\Service\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-22 08:00:00');

        AttedanceSetting::create([
            'late_tolerance_minutes' => 5,
            'require_location' => true,
        ]);

        WorkTime::create([
            'day_of_week' => 'selasa',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_check_in_rejects_before_work_time(): void
    {
        $employee = $this->createEmployee();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('09:00');

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 08:59:00',
        );
    }

    public function test_check_in_rejects_after_work_time_ends(): void
    {
        $employee = $this->createEmployee();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('17:00');

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 17:00:01',
        );
    }

    public function test_check_in_uses_tolerance_for_late_status(): void
    {
        $employee = $this->createEmployee();

        $attendance = app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:06:00',
        );

        $this->assertSame('late', $attendance->status);
        $this->assertSame(6, $attendance->late_minutes);
        $this->assertSame('2026-09-22', $attendance->date->toDateString());
    }

    public function test_pending_absence_request_does_not_block_check_in(): void
    {
        $employee = $this->createEmployee();

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-22',
            'type' => 'izin',
            'reason' => 'Keperluan keluarga',
            'status' => 'pending',
        ]);

        $attendance = app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );

        $this->assertSame('present', $attendance->status);
    }

    public function test_approved_absence_request_blocks_check_in(): void
    {
        $employee = $this->createEmployee();

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-22',
            'type' => 'sakit',
            'reason' => 'Demam',
            'status' => 'approved',
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('izin sakit');

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );
    }

    public function test_approved_leave_blocks_check_in(): void
    {
        $employee = $this->createEmployee();
        $contract = $employee->employeeContract()->firstOrFail();

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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('cuti');

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );
    }

    public function test_check_in_rejects_duplicate_check_in(): void
    {
        $employee = $this->createEmployee();

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('sudah melakukan check in');

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 10:00:00',
        );
    }

    public function test_check_out_calculates_work_duration(): void
    {
        $employee = $this->createEmployee();

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );

        $attendance = app(AttendanceService::class)->checkOut(
            employee: $employee,
            latitude: -6.91,
            longitude: 107.61,
            now: '2026-09-22 17:15:00',
        );

        $this->assertSame(495, $attendance->work_duration);
        $this->assertSame('2026-09-22 17:15:00', $attendance->check_out_at->format('Y-m-d H:i:s'));
    }

    public function test_check_out_requires_existing_check_in(): void
    {
        $employee = $this->createEmployee();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('belum melakukan check in');

        app(AttendanceService::class)->checkOut(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 17:15:00',
        );
    }

    public function test_check_out_rejects_duplicate_check_out(): void
    {
        $employee = $this->createEmployee();

        app(AttendanceService::class)->checkIn(
            employee: $employee,
            latitude: -6.9,
            longitude: 107.6,
            now: '2026-09-22 09:00:00',
        );

        app(AttendanceService::class)->checkOut(
            employee: $employee,
            latitude: -6.91,
            longitude: 107.61,
            now: '2026-09-22 17:00:00',
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('sudah melakukan check out');

        app(AttendanceService::class)->checkOut(
            employee: $employee,
            latitude: -6.91,
            longitude: 107.61,
            now: '2026-09-22 17:01:00',
        );
    }

    private function createEmployee(): Employees
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
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'salary_daily' => 100000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return $employee;
    }
}
