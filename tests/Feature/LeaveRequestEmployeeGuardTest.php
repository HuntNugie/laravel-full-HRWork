<?php

namespace Tests\Feature;

use App\Models\Employees;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveRequestEmployeeGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_without_an_employee_record_sees_an_empty_state(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole(Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']));

        $this->actingAs($user)
            ->get(route('leave.view'))
            ->assertOk()
            ->assertSee('Data karyawan belum tersedia')
            ->assertDontSee('Ajukan Cuti');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Page\Main\Leave\LeaveRequest::class)
            ->set('segment', 'pending')
            ->call('refresh')
            ->assertSee('Data karyawan belum tersedia');
    }

    public function test_employee_with_an_employee_record_can_open_personal_leave_page(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $permission = Permission::firstOrCreate(['name' => 'view-leave', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        Employees::create([
            'user_id' => $user->id,
            'employee_code' => 'TEST-LEAVE-001',
            'status_employee' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('leave.view'))
            ->assertOk()
            ->assertSee('Cuti Saya');
    }
}
