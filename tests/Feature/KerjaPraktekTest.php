<?php

namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use App\Service\ContractService;
use App\Support\HrAccess;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KerjaPraktekTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole($role);
        return $user;
    }

    public function test_schema_and_account_roles_are_separate(): void
    {
        $this->assertEqualsCanonicalizing(['administrator', 'hr'], Role::pluck('name')->all());
        $this->assertFalse(Schema::hasColumn('employees', 'user_id'));
        foreach (['attendances', 'payrolls', 'leave_requests', 'employee_resignations', 'employee_terminations'] as $table) {
            $this->assertFalse(Schema::hasTable($table));
        }
        $count = User::count();
        Employees::create(['name' => 'Employee Test', 'email' => 'employee@example.test']);
        $this->assertSame($count, User::count());
    }

    public function test_admin_and_hr_have_separate_permissions(): void
    {
        $admin = $this->account('administrator');
        $hr = $this->account('hr');
        $this->assertTrue($admin->can('manage-hr-access'));
        $this->assertFalse($admin->can('view-employee'));
        $this->actingAs($admin)->get(route('employee.view'))->assertForbidden();
        $this->actingAs($hr)->get(route('user.view'))->assertForbidden();
        $this->get(route('employee.view'))->assertForbidden();
        $hr->givePermissionTo('view-employee');
        $this->assertTrue($hr->fresh()->can('view-employee'));
        $hr->revokePermissionTo('view-employee');
        $this->assertFalse($hr->fresh()->can('view-employee'));
    }

    public function test_contract_activation_snapshot_and_termination(): void
    {
        $hr = $this->account('hr');
        $hr->givePermissionTo(HrAccess::hr());
        $this->actingAs($hr);
        $employee = Employees::create(['name' => 'Employee Test', 'email' => 'employee@example.test']);
        $position = Position::create(['name' => 'Developer', 'is_active' => 'active', 'min_salary_daily' => 100000]);
        $benefit = Benefit::create(['name' => 'Transport', 'description' => 'Original', 'status' => 'active']);
        $leave = LeaveType::create(['name' => 'Annual', 'default_days' => 12, 'gender' => 'all']);
        $input = [
            'positionId' => $position->id, 'contractType' => 'pkwt',
            'start_date' => today()->toDateString(), 'end_date' => today()->addMonth()->toDateString(),
            'salary_daily' => 100000, 'statusContract' => 'draft', 'note' => '',
            'benefitSelect' => [$benefit->id => ['selected' => true, 'amount' => 20000]],
            'dayLeave' => [$leave->id => 12],
        ];
        $service = app(ContractService::class);
        $contract = $service->save($employee, $input);
        $this->assertSame('draft', $contract->status);
        $input['statusContract'] = 'active';
        $contract = $service->save($employee, $input, $contract);
        $this->assertSame('active', $employee->fresh()->status_employee);
        $benefit->update(['name' => 'Changed']);
        $leave->update(['name' => 'Changed']);
        $this->assertSame('Transport', $contract->fresh()->benefits->first()->pivot->name_snapshot);
        $this->assertSame('Annual', $contract->fresh()->contractLeave->first()->name_snapshot);
        $service->terminate($contract, 'Test termination');
        $this->assertSame('terminated', $contract->fresh()->status);
        $this->assertSame('inactive', $employee->fresh()->status_employee);
    }
}
