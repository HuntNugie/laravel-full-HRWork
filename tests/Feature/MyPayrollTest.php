<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Payroll\DetailMyPayroll;
use App\Livewire\Page\Main\Payroll\MyPayroll;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MyPayrollTest extends TestCase
{
    use RefreshDatabase;

    private function makePermissionedEmployee(string $suffix): array
    {
        $user = User::factory()->create([
            'email' => 'employee-' . $suffix . '@example.test',
            'password' => Hash::make('password'),
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo([
            Permission::firstOrCreate([
                'name' => 'view-payroll-my',
                'guard_name' => 'web',
            ]),
            Permission::firstOrCreate([
                'name' => 'show-payroll-my',
                'guard_name' => 'web',
            ]),
        ]);

        $employee = Employees::create([
            'employee_code' => 'EMP-' . strtoupper($suffix),
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
        ]);

        $user->assignRole($role);

        $contract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-' . strtoupper($suffix),
            'employement_type' => 'pkwt',
            'start_date' => '2026-01-01',
            'end_date' => null,
            'salary_daily' => 100000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return [$user, $employee, $contract];
    }

    private function makePayroll(
        Employees $employee,
        string $suffix,
        string $payrollStatus = 'paid',
        string $periodStatus = 'paid',
    ): Payroll {
        $period = PayrollPeriod::create([
            'name' => 'Payroll ' . strtoupper($suffix),
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'payment_date' => '2026-09-30',
            'status' => $periodStatus,
        ]);

        $payroll = Payroll::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'employee_contract_id' => $employee->employeeContract()->firstOrFail()->id,
            'position_name' => 'Developer',
            'salary_daily' => 100000,
            'working_days' => 22,
            'present_days' => 20,
            'late_days' => 2,
            'absent_days' => 0,
            'paid_leave_days' => 2,
            'unpaid_leave_days' => 0,
            'paid_days' => 22,
            'gross_amount' => 4500000,
            'deduction_amount' => 100000,
            'net_amount' => 4400000,
            'status' => $payrollStatus,
            'notes' => 'Test payroll',
            'paid_at' => $payrollStatus === 'paid' ? now() : null,
        ]);

        PayrollItem::create([
            'payroll_id' => $payroll->id,
            'name' => 'Gaji Harian',
            'type' => 'earning',
            'amount' => 4000000,
            'quantity' => 20,
            'rate' => 200000,
            'source' => 'system',
            'description' => null,
            'sort_order' => 1,
        ]);

        PayrollItem::create([
            'payroll_id' => $payroll->id,
            'name' => 'Potongan Keterlambatan',
            'type' => 'deduction',
            'amount' => 100000,
            'quantity' => null,
            'rate' => null,
            'source' => 'system',
            'description' => null,
            'sort_order' => 2,
        ]);

        return $payroll;
    }

    public function test_employee_can_see_only_their_processed_or_paid_payrolls(): void
    {
        [$user, $employee] = $this->makePermissionedEmployee('ONE');
        [, $otherEmployee] = $this->makePermissionedEmployee('TWO');

        $ownPayroll = $this->makePayroll($employee, 'OWN', 'paid', 'paid');
        $this->makePayroll($employee, 'DRAFT', 'draft', 'draft');
        $this->makePayroll($otherEmployee, 'OTHER', 'paid', 'paid');

        $response = $this->actingAs($user)
            ->get(route('payroll.my.view'));

        $response
            ->assertOk()
            ->assertSee('Payroll OWN')
            ->assertDontSee('Payroll DRAFT')
            ->assertDontSee('Payroll OTHER');

        $this->assertDatabaseHas('payrolls', [
            'id' => $ownPayroll->id,
        ]);
    }

    public function test_employee_can_open_their_own_payroll_and_cannot_open_another_employee_payroll(): void
    {
        [$user, $employee] = $this->makePermissionedEmployee('ONE');
        [, $otherEmployee] = $this->makePermissionedEmployee('TWO');

        $ownPayroll = $this->makePayroll($employee, 'OWN', 'paid', 'paid');
        $otherPayroll = $this->makePayroll($otherEmployee, 'OTHER', 'paid', 'paid');

        $this->actingAs($user)
            ->get(route('payroll.my.show', $ownPayroll->id))
            ->assertOk()
            ->assertSee('Payroll OWN')
            ->assertSee('Total Diterima')
            ->assertSee('Gaji Harian');

        $this->actingAs($user)
            ->get(route('payroll.my.show', $otherPayroll->id))
            ->assertNotFound();
    }

    public function test_employee_must_have_payroll_permission_to_open_employee_payroll_pages(): void
    {
        [$user, $employee] = $this->makePermissionedEmployee('ONE');
        $user->syncRoles([]);
        $this->makePayroll($employee, 'OWN', 'paid', 'paid');

        $this->actingAs($user)
            ->get(route('payroll.my.view'))
            ->assertForbidden();
    }
}
