<?php

namespace Tests\Feature;

use App\Models\Attendances;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function makeEmployee(): array
    {
        $user = User::factory()->create([
            'name' => 'Dashboard Employee',
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        $employee = Employees::create([
            'employee_code' => 'EMP-DASH',
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);

        $contract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-DASH',
            'employement_type' => 'pkwt',
            'start_date' => '2026-01-01',
            'end_date' => null,
            'salary_daily' => 200000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return [$user, $employee, $contract];
    }

    public function test_employee_dashboard_uses_live_employee_data(): void
    {
        Carbon::setTestNow('2026-09-21 10:00:00');

        [$user, $employee] = $this->makeEmployee();

        WorkTime::create([
            'day_of_week' => 'senin',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => true,
        ]);

        Attendances::create([
            'employee_id' => $employee->id,
            'date' => '2026-09-21',
            'check_in_at' => '2026-09-21 08:10:00',
            'check_out_at' => '2026-09-21 17:00:00',
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $period = PayrollPeriod::create([
            'name' => 'Payroll September 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'payment_date' => '2026-09-30',
            'status' => 'paid',
        ]);

        Payroll::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'employee_contract_id' => $employee->employeeContract()->firstOrFail()->id,
            'position_name' => 'Developer',
            'salary_daily' => 200000,
            'working_days' => 1,
            'present_days' => 1,
            'late_days' => 0,
            'absent_days' => 0,
            'paid_leave_days' => 0,
            'unpaid_leave_days' => 0,
            'paid_days' => 1,
            'gross_amount' => 200000,
            'deduction_amount' => 0,
            'net_amount' => 200000,
            'status' => 'paid',
            'notes' => null,
            'paid_at' => '2026-09-30 09:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Employee')
            ->assertSee('Dashboard Employee')
            ->assertSee('Hadir')
            ->assertSee('1')
            ->assertSee('Rp200.000')
            ->assertSee('Developer')
            ->assertSee('EMP-DASH')
            ->assertSee('Presensi Hari Ini')
            ->assertSee('Payroll Terbaru');
    }

    public function test_non_employee_dashboard_remains_available(): void
    {
        Carbon::setTestNow('2026-09-21 10:00:00');

        $user = User::factory()->create([
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'HR',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Role belum ditetapkan');
    }
}
