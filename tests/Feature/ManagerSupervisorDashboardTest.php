<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\WorkManagement\CreateTask;
use App\Models\Attendances;
use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagerSupervisorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_only_shows_employees_from_their_division_teams(): void
    {
        [$managerUser, $manager, $division, $team, $outsideEmployee] = $this->makeManagerStructure();

        $insideUser = User::factory()->create(['name' => 'Inside Employee', 'status' => 'active']);
        $inside = $this->makeEmployee('INSIDE', $insideUser);
        $inside->update(['team_id' => $team->id]);

        Attendances::create([
            'employee_id' => $inside->id,
            'date' => today(),
            'check_in_at' => now()->setTime(9, 0),
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $this->actingAs($managerUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Manager')
            ->assertSee('Inside Employee')
            ->assertDontSee($outsideEmployee->user->name);
    }

    public function test_supervisor_dashboard_only_shows_members_of_the_supervised_team(): void
    {
        [$supervisorUser, $supervisor, $team, $worker, $outsideWorker] = $this->makeSupervisorStructure();

        $this->actingAs($supervisorUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Supervisor')
            ->assertSee($worker->user->name)
            ->assertDontSee($outsideWorker->user->name);
    }

    public function test_supervisor_create_task_uses_the_supervised_team_automatically(): void
    {
        [$supervisorUser, $supervisor, $team, $worker] = $this->makeSupervisorStructure();

        $gmUser = User::factory()->create(['name' => 'GM', 'status' => 'active']);
        $gm = $this->makeRoleEmployee('GM', $gmUser, 'general-manager');

        $division = $team->divisi;
        $master = MasterProject::create([
            'created_by' => $gm->id,
            'name' => 'Master Project',
            'description' => null,
            'status' => 'in_progress',
        ]);

        $divisionProject = DivisionProject::create([
            'master_project_id' => $master->id,
            'divisi_id' => $division->id,
            'manager_id' => $division->manager_id,
            'created_by' => $gm->id,
            'name' => 'Division Project',
            'description' => null,
            'is_required' => true,
            'manual_progress' => 0,
            'status' => 'in_progress',
        ]);

        $divisionProject->teams()->attach($team->id, ['assigned_by' => $gm->id]);

        $this->actingAs($supervisorUser);

        Livewire::test(CreateTask::class, ['divisionProject' => $divisionProject])
            ->assertSet('isSupervisor', true)
            ->assertSet('team_id', $team->id)
            ->assertSee($worker->user->name)
            ->assertDontSee('Pilih team');
    }

    private function makeManagerStructure(): array
    {
        $managerUser = User::factory()->create([
            'name' => 'Manager One',
            'status' => 'active',
        ]);
        $manager = $this->makeRoleEmployee('MANAGER', $managerUser, 'manager');

        $division = Divisi::create([
            'name' => 'Development',
            'description' => 'Development',
            'is_active' => 'active',
            'manager_id' => $manager->id,
        ]);

        $team = Team::create([
            'name' => 'Backend',
            'description' => 'Backend',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => null,
        ]);

        $outsideUser = User::factory()->create([
            'name' => 'Outside Employee',
            'status' => 'active',
        ]);
        $outsideEmployee = $this->makeRoleEmployee('OUTSIDE', $outsideUser, 'task-worker');

        return [$managerUser, $manager, $division, $team, $outsideEmployee];
    }

    private function makeSupervisorStructure(): array
    {
        $supervisorUser = User::factory()->create([
            'name' => 'Supervisor One',
            'status' => 'active',
        ]);
        $supervisor = $this->makeRoleEmployee('SUPERVISOR', $supervisorUser, 'supervisor');

        $managerUser = User::factory()->create([
            'name' => 'Manager One',
            'status' => 'active',
        ]);
        $manager = $this->makeRoleEmployee('MANAGER', $managerUser, 'manager');

        $division = Divisi::create([
            'name' => 'Development',
            'description' => 'Development',
            'is_active' => 'active',
            'manager_id' => $manager->id,
        ]);

        $team = Team::create([
            'name' => 'Backend',
            'description' => 'Backend',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $supervisor->update(['team_id' => $team->id]);

        $workerUser = User::factory()->create([
            'name' => 'Team Worker',
            'status' => 'active',
        ]);
        $worker = $this->makeRoleEmployee('WORKER', $workerUser, 'task-worker');
        $worker->update(['team_id' => $team->id]);

        $outsideTeam = Team::create([
            'name' => 'Other Team',
            'description' => 'Other Team',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => null,
        ]);

        $outsideUser = User::factory()->create([
            'name' => 'Other Worker',
            'status' => 'active',
        ]);
        $outsideWorker = $this->makeRoleEmployee('OUTSIDE', $outsideUser, 'task-worker');
        $outsideWorker->update(['team_id' => $outsideTeam->id]);

        return [$supervisorUser, $supervisor, $team, $worker, $outsideWorker];
    }

    private function makeRoleEmployee(string $suffix, User $user, string $roleName): Employees
    {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $permissions = match ($roleName) {
            'manager' => [
                'view-status-daily',
                'view-master-project',
                'view-division-project',
                'assign-project-team',
                'report-project-progress',
                'review-division-project',
                'view-task',
                'assign-task',
                'update-task',
            ],
            'supervisor' => [
                'view-status-daily',
                'view-master-project',
                'view-division-project',
                'report-project-progress',
                'view-task',
                'create-task',
                'assign-task',
                'update-task',
                'review-task',
            ],
            'task-worker' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
                'submit-task',
            ],
            'general-manager' => [
                'view-master-project',
                'create-master-project',
                'approve-master-project',
                'view-division-project',
                'create-division-project',
                'view-task',
                'create-task',
                'assign-task',
                'update-task',
                'update-own-task',
                'submit-task',
                'review-task',
            ],
            default => [],
        };

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
            $role->givePermissionTo($permission);
        }

        $user->assignRole($role);

        return Employees::create([
            'employee_code' => 'EMP-' . $suffix,
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);
    }

    private function makeEmployee(string $suffix, User $user): Employees
    {
        return Employees::create([
            'employee_code' => 'EMP-' . $suffix,
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);
    }
}
