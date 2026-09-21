<?php

namespace Tests\Feature;

use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\WorkTime;
use App\Models\EmployeeAbsenceRequest;
use App\Models\UnpresentDisciplineRule;
use App\Service\UnpresentDisciplineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnpresentDisciplineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-21 10:00:00');

        Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web',
        ]);

        UnpresentDisciplineRule::create([
            'threshold' => 3,
            'period_type' => 'monthly',
            'description' => 'Minimal 3 unpresent dalam bulan berjalan.',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_only_past_unpresent_daily_statuses_are_counted_as_candidates(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-01',
            endDate: null,
            contractStatus: 'active',
        );

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');

        $candidate = app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->firstWhere('employee.id', $employee->id);

        $this->assertNotNull($candidate);
        $this->assertSame(5, $candidate['unpresent_count']);
        $this->assertSame(
            ['2026-09-01', '2026-09-07', '2026-09-08', '2026-09-14', '2026-09-15'],
            $candidate['dates']
        );
        $this->assertSame('2026-09-01', $candidate['period_start']);
        $this->assertSame('2026-09-21', $candidate['period_end']);
    }

    public function test_below_threshold_is_not_returned_as_candidate(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-01',
            endDate: '2026-09-08',
            contractStatus: 'expired',
        );

        $this->createWorkingTime('senin');

        $candidates = app(UnpresentDisciplineService::class)
            ->getCandidates();

        $this->assertNull(
            $candidates->firstWhere('employee.id', $employee->id)
        );
    }

    public function test_approved_leave_sick_permit_holiday_and_non_working_day_are_not_counted_as_unpresent(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-01',
            endDate: null,
            contractStatus: 'active',
        );

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa', false);

        Holidays::create([
            'name' => 'Hari Libur',
            'date' => '2026-09-14',
        ]);

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
            'start_date' => '2026-09-07',
            'end_date' => '2026-09-07',
            'total_days' => 1,
            'status' => 'approved',
        ]);

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-15',
            'type' => 'sakit',
            'reason' => 'Demam',
            'status' => 'approved',
        ]);

        $this->createWorkingTime('rabu');

        EmployeeAbsenceRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-16',
            'type' => 'izin',
            'reason' => 'Keperluan keluarga',
            'status' => 'approved',
        ]);

        $candidate = app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->firstWhere('employee.id', $employee->id);

        $this->assertNull($candidate);
    }

    public function test_current_day_is_pending_and_does_not_make_candidate(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-21',
            endDate: null,
            contractStatus: 'active',
        );

        $this->createWorkingTime('senin');

        $state = app(\App\Service\EmployeeDailyStatusService::class)
            ->getStatus($employee, '2026-09-21');

        $this->assertSame('pending', $state['status']);

        $this->assertNull(
            app(UnpresentDisciplineService::class)
                ->getCandidates()
                ->firstWhere('employee.id', $employee->id)
        );
    }

    public function test_expired_contract_can_still_produce_historical_unpresent_candidate(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-01',
            endDate: '2026-09-10',
            contractStatus: 'expired',
        );

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');

        $candidate = app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->firstWhere('employee.id', $employee->id);

        $this->assertNotNull($candidate);
        $this->assertSame(3, $candidate['unpresent_count']);
        $this->assertSame(
            ['2026-09-01', '2026-09-07', '2026-09-08'],
            $candidate['dates']
        );
    }

    public function test_contract_transition_keeps_unpresent_dates_from_both_effective_contracts(): void
    {
        $employee = $this->createEmployee(
            startDate: '2026-09-01',
            endDate: '2026-09-15',
            contractStatus: 'expired',
        );

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-' . uniqid(),
            'employement_type' => 'pkwtt',
            'start_date' => '2026-09-16',
            'end_date' => null,
            'salary_daily' => 150000,
            'status' => 'active',
            'position_name' => 'Senior Developer',
            'notes' => null,
        ]);

        $this->createWorkingTime('senin');
        $this->createWorkingTime('selasa');

        $candidate = app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->firstWhere('employee.id', $employee->id);

        $this->assertNotNull($candidate);
        $this->assertSame(5, $candidate['unpresent_count']);
        $this->assertSame(
            ['2026-09-01', '2026-09-07', '2026-09-08', '2026-09-14', '2026-09-15'],
            $candidate['dates']
        );
    }

    public function test_invalid_threshold_is_rejected(): void
    {
        UnpresentDisciplineRule::query()->firstOrFail()->update([
            'threshold' => 0,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Threshold ketidakhadiran harus lebih besar dari 0.'
        );

        app(UnpresentDisciplineService::class)->getCandidates();
    }

    public function test_only_monthly_period_is_supported(): void
    {
        UnpresentDisciplineRule::query()->firstOrFail()->update([
            'period_type' => 'weekly',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Periode aturan ketidakhadiran saat ini harus bulanan.'
        );

        app(UnpresentDisciplineService::class)->getCandidates();
    }

    private function createEmployee(
        string $startDate,
        ?string $endDate,
        string $contractStatus,
    ): Employees {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $employee = Employees::create([
            'employee_code' => 'EMP-' . uniqid(),
            'user_id' => $user->id,
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
            'status' => $contractStatus,
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return $employee;
    }

    private function createWorkingTime(
        string $dayOfWeek,
        bool $isWorkingDay = true
    ): WorkTime {
        return WorkTime::create([
            'day_of_week' => $dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => $isWorkingDay,
        ]);
    }
}
