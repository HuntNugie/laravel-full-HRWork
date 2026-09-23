<?php

namespace Tests\Feature;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MyWarningLetterTest extends TestCase
{
    use RefreshDatabase;

    private function makeEmployee(string $suffix): array
    {
        $user = User::factory()->create([
            'name' => 'Employee ' . $suffix,
            'email' => 'employee-' . strtolower($suffix) . '@example.test',
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo([
            Permission::firstOrCreate([
                'name' => 'view-warning-letter-my',
                'guard_name' => 'web',
            ]),
            Permission::firstOrCreate([
                'name' => 'show-warning-letter-my',
                'guard_name' => 'web',
            ]),
        ]);

        $user->assignRole($role);

        $employee = Employees::create([
            'employee_code' => 'EMP-' . strtoupper($suffix),
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);

        return [$user, $employee];
    }

    private function makeWarningLetter(Employees $employee, string $suffix, string $status = 'issued'): EmployeeWarningLetter
    {
        return EmployeeWarningLetter::create([
            'employee_id' => $employee->id,
            'warning_level' => 'SP1',
            'letter_number' => 'SP/2026/09/' . $suffix,
            'issued_date' => '2026-09-20',
            'reason' => 'Pelanggaran test ' . $suffix,
            'description' => null,
            'status' => $status,
            'created_by' => null,
            'issued_by' => null,
            'issued_at' => $status === 'issued' ? now() : null,
            'cancelled_by' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    public function test_employee_sees_only_their_issued_warning_letters(): void
    {
        [$user, $employee] = $this->makeEmployee('ONE');
        [, $otherEmployee] = $this->makeEmployee('TWO');

        $this->makeWarningLetter($employee, '001', 'issued');
        $this->makeWarningLetter($employee, '002', 'draft');
        $this->makeWarningLetter($employee, '003', 'cancelled');
        $this->makeWarningLetter($otherEmployee, '004', 'issued');

        $this->actingAs($user)
            ->get(route('warning-letter.my.view'))
            ->assertOk()
            ->assertSee('SP/2026/09/001')
            ->assertDontSee('SP/2026/09/002')
            ->assertDontSee('SP/2026/09/003')
            ->assertDontSee('SP/2026/09/004');
    }

    public function test_employee_can_open_only_their_own_issued_warning_letter(): void
    {
        [$user, $employee] = $this->makeEmployee('ONE');
        [, $otherEmployee] = $this->makeEmployee('TWO');

        $own = $this->makeWarningLetter($employee, '001', 'issued');
        $other = $this->makeWarningLetter($otherEmployee, '002', 'issued');
        $draft = $this->makeWarningLetter($employee, '003', 'draft');

        $this->actingAs($user)
            ->get(route('warning-letter.my.show', $own))
            ->assertOk()
            ->assertSee('SP/2026/09/001')
            ->assertSee('Pelanggaran test 001');

        $this->actingAs($user)
            ->get(route('warning-letter.my.show', $other))
            ->assertNotFound();

        $this->actingAs($user)
            ->get(route('warning-letter.my.show', $draft))
            ->assertNotFound();
    }

    public function test_employee_warning_letter_pages_require_their_permissions(): void
    {
        [$user] = $this->makeEmployee('ONE');
        $user->syncRoles([]);

        $this->actingAs($user)
            ->get(route('warning-letter.my.view'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('warning-letter.my.show', 999999))
            ->assertForbidden();
    }
}
