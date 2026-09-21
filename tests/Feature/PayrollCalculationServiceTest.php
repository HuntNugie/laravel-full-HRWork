<?php

namespace Tests\Feature;

use App\Models\AttedanceSetting;
use App\Models\Attendances;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LateDisciplineRule;
use App\Models\PayrollPeriod;
use App\Models\WorkTime;
use App\Service\PayrollCalculationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-23 10:00:00');

        AttedanceSetting::create([
            'late_tolerance_minutes' => 5,
            'require_location' => true,
        ]);

        LateDisciplineRule::create([
            'threshold' => 3,
            'period_type' => 'monthly',
            'action_type' => 'deduction',
            'action_amount' => 20000,
            'status' => 'active',
            'description' => 'Test rule',
        ]);

        foreach ([
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jumat',
        ] as $day) {
            WorkTime::create([
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_working_day' => true,
            ]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_pending_current_and_future_days_are_not_counted_as_absent(): void
    {
        $employee = $this->createEmployee();

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-21',
            'check_in_at' => '2026-09-21 09:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $period = PayrollPeriod::create([
            'name' => 'September 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'payment_date' => null,
            'status' => 'draft',
            'created_by' => null,
        ]);

        $calculation = app(PayrollCalculationService::class)->calculate(
            employee: $employee,
            contract: $employee->employeeContract()->firstOrFail(),
            period: $period,
        );

        /*
         * 21 Sep = present (resolved working day)
         * 22 Sep = unpresent (resolved working day)
         * 23 Sep = current/pending, must not count
         * 24-30 Sep = future/pending, must not count
         */
        $this->assertSame(2, $calculation['working_days']);
        $this->assertSame(1, $calculation['present_days']);
        $this->assertSame(1, $calculation['paid_days']);
        $this->assertSame(1, $calculation['absent_days']);
    }

    public function test_paid_days_are_present_plus_paid_leave(): void
    {
        $employee = $this->createEmployee();

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-21',
            'check_in_at' => '2026-09-21 09:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $contract = $employee->employeeContract()->firstOrFail();

        $leaveType = \App\Models\LeaveType::create([
            'name' => 'Cuti Tahunan',
            'default_days' => 12,
            'gender' => 'all',
            'status' => 'active',
        ]);

        \App\Models\LeaveRequest::create([
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-22',
            'end_date' => '2026-09-22',
            'total_days' => 1,
            'status' => 'approved',
        ]);

        $period = PayrollPeriod::create([
            'name' => 'September 2026',
            'start_date' => '2026-09-21',
            'end_date' => '2026-09-22',
            'payment_date' => null,
            'status' => 'draft',
            'created_by' => null,
        ]);

        $calculation = app(PayrollCalculationService::class)->calculate(
            employee: $employee,
            contract: $contract,
            period: $period,
        );

        $this->assertSame(2, $calculation['working_days']);
        $this->assertSame(1, $calculation['present_days']);
        $this->assertSame(1, $calculation['paid_leave_days']);
        $this->assertSame(2, $calculation['paid_days']);
        $this->assertSame(0, $calculation['absent_days']);
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
