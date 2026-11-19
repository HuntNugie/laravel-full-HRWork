<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Employees;
use App\Models\User;
use App\Livewire\Components\Main\User\ModalChangePassword;
use App\Livewire\Components\Main\User\ModalEditRole;
use App\Livewire\Page\Main\User\DetailUser;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HRISOrganizationRetirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_retired_structure_is_removed_and_position_contract_data_remains_active(): void
    {
        $this->assertFalse(Schema::hasTable('teams'));
        $this->assertFalse(Schema::hasTable('divisis'));
        $this->assertFalse(Schema::hasTable('archive_teams'));
        $this->assertFalse(Schema::hasTable('archive_divisis'));
        $this->assertFalse(Schema::hasColumn('employees', 'team_id'));
        $this->assertFalse(Schema::hasColumn('employees', 'legacy_team_id'));
        $this->assertTrue(Schema::hasTable('positions'));
        $this->assertTrue(Schema::hasTable('position_jobdesks'));
        $this->assertTrue(Schema::hasColumn('employees', 'position_id'));
        $this->assertTrue(Schema::hasColumn('employee_contracts', 'position_name'));

        Position::create(['name' => 'HR Officer', 'min_salary_daily' => 200000, 'is_active' => 'active']);
        $this->assertDatabaseHas('positions', ['name' => 'HR Officer']);
    }

    public function test_roles_permissions_and_superadmin_bypass_follow_hris_scope(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);

        $this->assertSame(0, Role::query()->whereIn('name', ['manager', 'supervisor'])->count());
        $this->assertSame(0, Permission::query()->whereIn('name', [
            'view-divisi', 'view-team', 'assign-team-employee',
        ])->count());

        $hr = User::factory()->create();
        $hr->assignRole('hr');
        $this->assertTrue($hr->can('view-employee'));
        $this->assertTrue($hr->can('view-position'));
        $this->assertTrue($hr->can('view-termination'));
        $this->assertTrue(Gate::forUser($hr)->allows('create', Employees::class));
        $this->assertFalse($hr->can('create-user'));
        $this->assertFalse($hr->can('reset-user-password'));
        $this->assertFalse($hr->can('assign-role'));

        $administrator = User::factory()->create();
        $administrator->assignRole('administrator');
        $this->assertTrue($administrator->can('reset-user-password'));
        $this->assertTrue($administrator->can('change-user-status'));
        $this->assertTrue($administrator->can('assign-role'));
        $this->assertFalse($administrator->can('view-employee'));
        $this->assertFalse(Gate::forUser($administrator)->allows('update', Role::findByName('super-admin')));

        $superadmin = User::factory()->create();
        $superadmin->assignRole('super-admin');
        $this->assertTrue($superadmin->can('view-employee'));
        $this->assertTrue(Gate::forUser($superadmin)->allows('update', Role::findByName('super-admin')));
        $this->assertTrue(Gate::forUser($superadmin)->allows('delete', Role::findByName('super-admin')));
    }

    public function test_hr_cannot_manage_password_or_status_and_admin_cannot_assign_superadmin(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);

        $hr = User::factory()->create();
        $hr->assignRole('hr');
        $administrator = User::factory()->create();
        $administrator->assignRole('administrator');
        $target = User::factory()->create();

        Livewire::actingAs($hr)
            ->test(DetailUser::class, ['user' => $target])
            ->call('actionStatusAccount')
            ->assertForbidden();

        Livewire::actingAs($hr)
            ->test(ModalChangePassword::class, ['user' => $target])
            ->set('newPassword', 'PasswordBaru123')
            ->set('password_confirmation', 'PasswordBaru123')
            ->call('changePassword')
            ->assertForbidden();

        Livewire::actingAs($administrator)
            ->test(ModalEditRole::class, ['user' => $target])
            ->set('roleName', ['super-admin'])
            ->call('changeRole')
            ->assertForbidden();
    }
}
