<?php

namespace Tests\Feature;

use App\Livewire\Components\Main\Divisi\FormAdd;
use App\Livewire\Components\Main\Divisi\FormEdit;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DivisionManagerTest extends TestCase
{
    use RefreshDatabase;

    private function makeAuthorizedUser(): User
    {
        $user = User::factory()->create();

        $role = Role::firstOrCreate([
            'name' => 'hr',
            'guard_name' => 'web',
        ]);

        foreach (['create-divisi', 'update-divisi', 'view-divisi'] as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            $role->givePermissionTo($permission);
        }

        $user->assignRole($role);

        return $user;
    }

    private function makeEmployee(string $suffix, User $user): Employees
    {
        return Employees::create([
            'employee_code' => 'EMP-' . strtoupper($suffix),
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);
    }

    public function test_division_can_store_and_update_manager(): void
    {
        $admin = $this->makeAuthorizedUser();
        $managerUser = User::factory()->create([
            'name' => 'Budi Manager',
        ]);

        $manager = $this->makeEmployee('MGR', $managerUser);

        $this->actingAs($admin);

        Livewire::test(FormAdd::class)
            ->set('name', 'Technology')
            ->set('desc', 'Technology Division')
            ->set('managerId', $manager->id)
            ->call('store')
            ->assertHasNoErrors();

        $division = Divisi::where('name', 'Technology')->firstOrFail();

        $this->assertDatabaseHas('divisis', [
            'id' => $division->id,
            'manager_id' => $manager->id,
        ]);

        $this->assertSame($manager->id, $division->manager->id);
        $this->assertSame($division->id, $manager->managedDivisi->id);

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->set('name', 'Technology')
            ->set('desc', 'Technology Division Updated')
            ->set('managerId', null)
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('divisis', [
            'id' => $division->id,
            'manager_id' => null,
        ]);
    }
    public function test_manager_assigned_to_another_division_is_not_available(): void
    {
        $admin = $this->makeAuthorizedUser();

        $managerUser = User::factory()->create([
            'name' => 'Budi Manager',
        ]);
        $manager = $this->makeEmployee('MGR', $managerUser);

        $otherUser = User::factory()->create([
            'name' => 'Sinta Manager',
        ]);
        $otherManager = $this->makeEmployee('MGR2', $otherUser);

        $firstDivision = Divisi::create([
            'name' => 'Technology',
            'description' => 'Technology Division',
            'is_active' => 'active',
            'manager_id' => $manager->id,
        ]);

        $secondDivision = Divisi::create([
            'name' => 'Operations',
            'description' => 'Operations Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(FormAdd::class)
            ->assertDontSee('Budi Manager')
            ->assertSee('Sinta Manager');

        Livewire::test(FormEdit::class)
            ->call('open', $secondDivision->id)
            ->assertDontSee('Budi Manager')
            ->assertSee('Sinta Manager');

        Livewire::test(FormEdit::class)
            ->call('open', $firstDivision->id)
            ->assertSee('Budi Manager');

        Livewire::test(FormAdd::class)
            ->set('name', 'Finance')
            ->set('desc', 'Finance Division')
            ->set('managerId', $manager->id)
            ->call('store')
            ->assertHasErrors(['managerId']);

        Livewire::test(FormEdit::class)
            ->call('open', $secondDivision->id)
            ->set('name', 'Operations')
            ->set('desc', 'Operations Division')
            ->set('managerId', $manager->id)
            ->call('update')
            ->assertHasErrors(['managerId']);
    }

    public function test_form_edit_prioritizes_manager_position_when_available(): void
    {
        $admin = $this->makeAuthorizedUser();

        $division = Divisi::create([
            'name' => 'Technology',
            'description' => 'Technology Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $managerPosition = Position::create([
            'name' => 'Manager',
            'description' => 'Manager position',
        ]);

        $staffPosition = Position::create([
            'name' => 'Staff',
            'description' => 'Staff position',
        ]);

        $managerUser = User::factory()->create(['name' => 'Priority Manager']);
        $manager = $this->makeEmployee('PRIORITY-MGR', $managerUser);
        $manager->update(['position_id' => $managerPosition->id]);

        $staffUser = User::factory()->create(['name' => 'Fallback Staff']);
        $staff = $this->makeEmployee('FALLBACK-STAFF', $staffUser);
        $staff->update(['position_id' => $staffPosition->id]);

        $this->actingAs($admin);

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->assertSee('Priority Manager')
            ->assertDontSee('Fallback Staff');
    }

    public function test_form_edit_allows_other_position_when_no_manager_position_is_available(): void
    {
        $admin = $this->makeAuthorizedUser();

        $division = Divisi::create([
            'name' => 'Operations',
            'description' => 'Operations Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $staffPosition = Position::create([
            'name' => 'Senior Staff',
            'description' => 'Senior staff position',
        ]);

        $staffUser = User::factory()->create(['name' => 'Senior Staff Candidate']);
        $staff = $this->makeEmployee('SENIOR-STAFF', $staffUser);
        $staff->update(['position_id' => $staffPosition->id]);

        $this->actingAs($admin);

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->assertSee('Senior Staff Candidate');
    }


    public function test_current_manager_does_not_block_fallback_to_other_positions(): void
    {
        $admin = $this->makeAuthorizedUser();

        $division = Divisi::create([
            'name' => 'Operations',
            'description' => 'Operations Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $managerPosition = Position::create([
            'name' => 'Manager',
            'description' => 'Manager position',
        ]);

        $staffPosition = Position::create([
            'name' => 'Senior Staff',
            'description' => 'Senior staff position',
        ]);

        $currentManagerUser = User::factory()->create(['name' => 'Current Manager']);
        $currentManager = $this->makeEmployee('CURRENT-MGR', $currentManagerUser);
        $currentManager->update(['position_id' => $managerPosition->id]);

        $staffUser = User::factory()->create(['name' => 'Staff Candidate']);
        $staff = $this->makeEmployee('STAFF-CANDIDATE', $staffUser);
        $staff->update(['position_id' => $staffPosition->id]);

        $division->update(['manager_id' => $currentManager->id]);

        $this->actingAs($admin);

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->assertSee('Current Manager')
            ->assertSee('Staff Candidate');
    }

    public function test_current_manager_is_not_the_only_priority_manager_when_another_manager_exists(): void
    {
        $admin = $this->makeAuthorizedUser();

        $division = Divisi::create([
            'name' => 'Finance',
            'description' => 'Finance Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $managerPosition = Position::create([
            'name' => 'Manager',
            'description' => 'Manager position',
        ]);

        $staffPosition = Position::create([
            'name' => 'Staff',
            'description' => 'Staff position',
        ]);

        $currentManagerUser = User::factory()->create(['name' => 'Current Manager']);
        $currentManager = $this->makeEmployee('CURRENT-MGR-2', $currentManagerUser);
        $currentManager->update(['position_id' => $managerPosition->id]);

        $replacementManagerUser = User::factory()->create(['name' => 'Replacement Manager']);
        $replacementManager = $this->makeEmployee('REPLACEMENT-MGR', $replacementManagerUser);
        $replacementManager->update(['position_id' => $managerPosition->id]);

        $staffUser = User::factory()->create(['name' => 'Staff Candidate']);
        $staff = $this->makeEmployee('STAFF-CANDIDATE-2', $staffUser);
        $staff->update(['position_id' => $staffPosition->id]);

        $division->update(['manager_id' => $currentManager->id]);

        $this->actingAs($admin);

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->assertSee('Current Manager')
            ->assertSee('Replacement Manager')
            ->assertDontSee('Staff Candidate');
    }

    public function test_employee_with_a_team_cannot_become_division_manager(): void
    {
        $admin = $this->makeAuthorizedUser();

        $division = Divisi::create([
            'name' => 'Technology',
            'description' => 'Technology Division',
            'is_active' => 'active',
            'manager_id' => null,
        ]);

        $team = Team::create([
            'name' => 'Backend',
            'description' => 'Backend Team',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => null,
        ]);

        $memberUser = User::factory()->create([
            'name' => 'Andi Team Member',
        ]);
        $member = $this->makeEmployee('TEAM', $memberUser);
        $member->update(['team_id' => $team->id]);

        $supervisorUser = User::factory()->create([
            'name' => 'Rina Supervisor',
        ]);
        $supervisor = $this->makeEmployee('SUP', $supervisorUser);
        $supervisor->update(['team_id' => $team->id]);
        $team->update(['supervisor_id' => $supervisor->id]);

        $this->actingAs($admin);

        Livewire::test(FormAdd::class)
            ->assertDontSee('Andi Team Member')
            ->assertDontSee('Rina Supervisor');

        Livewire::test(FormEdit::class)
            ->call('open', $division->id)
            ->assertDontSee('Andi Team Member')
            ->assertDontSee('Rina Supervisor');

        Livewire::test(FormAdd::class)
            ->set('name', 'Operations')
            ->set('desc', 'Operations Division')
            ->set('managerId', $member->id)
            ->call('store')
            ->assertHasErrors(['managerId']);

        Livewire::test(FormAdd::class)
            ->set('name', 'Sales')
            ->set('desc', 'Sales Division')
            ->set('managerId', $supervisor->id)
            ->call('store')
            ->assertHasErrors(['managerId']);
    }

}
