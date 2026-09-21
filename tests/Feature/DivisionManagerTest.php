<?php

namespace Tests\Feature;

use App\Livewire\Components\Main\Divisi\FormAdd;
use App\Livewire\Components\Main\Divisi\FormEdit;
use App\Models\Divisi;
use App\Models\Employees;
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
            'name' => 'HR',
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
}
