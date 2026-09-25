<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Service\WorkManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkManagementPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_only_view_their_division_projects(): void
    {
        [$managerA, $managerB, $gm, $divisionA, $divisionB] = $this->makeManagers();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');

        $projectA = $service->createDivisionProject($master, $divisionA, $gm, 'Project A');
        $projectB = $service->createDivisionProject($master, $divisionB, $gm, 'Project B');

        $this->assertTrue(Gate::forUser($managerA->user)->allows('view', $projectA));
        $this->assertFalse(Gate::forUser($managerA->user)->allows('view', $projectB));
        $this->assertTrue(Gate::forUser($managerB->user)->allows('view', $projectB));
    }

    public function test_task_worker_can_only_toggle_own_task(): void
    {
        [$gm, $manager, $supervisor, $worker, $otherWorker, $team, $division] = $this->makeTaskScope();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project', initialTeam: $team);
        $task = $service->createTask($project, $team, $worker, $supervisor, 'Owned task');

        $this->assertTrue(Gate::forUser($worker->user)->allows('updateOwn', $task));
        $this->assertFalse(Gate::forUser($otherWorker->user)->allows('updateOwn', $task));
    }

    public function test_only_supervisor_has_create_task_permission(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $this->assertFalse(Gate::forUser($gm->user)->allows('create', Task::class));
        $this->assertFalse(Gate::forUser($manager->user)->allows('create', Task::class));
        $this->assertTrue(Gate::forUser($supervisor->user)->allows('create', Task::class));
        $this->assertFalse(Gate::forUser($worker->user)->allows('create', Task::class));
    }

    public function test_manager_can_submit_division_completion_but_not_review_it(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project', initialTeam: $team);

        $this->assertTrue(Gate::forUser($manager->user)->allows('submitForCompletion', $project));
        $this->assertFalse(Gate::forUser($manager->user)->allows('reviewCompletion', $project));
        $this->assertTrue(Gate::forUser($gm->user)->allows('reviewCompletion', $project));
    }

    public function test_supervisor_can_view_only_their_team_page(): void
    {
        [$gm, $manager, $supervisor, $worker, $otherSupervisor, $otherWorker, $team, $otherTeam, $division] = $this->makeTwoTeams();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project', initialTeam: $team);
        $service->assignTeam($project, $otherTeam, $manager);

        $this->assertTrue(
            Gate::forUser($supervisor->user)->allows('viewTeam', [$project, $team])
        );

        $this->assertFalse(
            Gate::forUser($supervisor->user)->allows('viewTeam', [$project, $otherTeam])
        );

        $this->assertTrue(
            Gate::forUser($manager->user)->allows('viewTeam', [$project, $otherTeam])
        );
    }

    public function test_gm_and_manager_can_assign_team_but_worker_cannot(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $this->assertTrue(
            Gate::forUser($gm->user)->allows('assignTeam', $this->makeDivisionProject($gm, $division))
        );

        $project = $this->makeDivisionProject($gm, $division);
        $this->assertTrue(Gate::forUser($manager->user)->allows('assignTeam', $project));
        $this->assertFalse(Gate::forUser($worker->user)->allows('assignTeam', $project));

        $this->assertNotNull($supervisor);
        $this->assertNotNull($team);
    }

    private function makeDivisionProject(Employees $gm, Divisi $division): \App\Models\DivisionProject
    {
        $master = app(WorkManagementService::class)->createMasterProject($gm, uniqid('Master ', true));
        return app(WorkManagementService::class)->createDivisionProject(
            $master,
            $division,
            $gm,
            uniqid('Division ', true),
        );
    }

    private function makeManagers(): array
    {
        $gm = $this->makeRoleUser('GM1', 'general-manager');
        $managerA = $this->makeRoleUser('MAN1', 'manager');
        $managerB = $this->makeRoleUser('MAN2', 'manager');

        $divisionA = Divisi::create([
            'name' => 'Division A',
            'description' => 'A',
            'is_active' => 'active',
            'manager_id' => $managerA->id,
        ]);

        $divisionB = Divisi::create([
            'name' => 'Division B',
            'description' => 'B',
            'is_active' => 'active',
            'manager_id' => $managerB->id,
        ]);

        return [$managerA, $managerB, $gm, $divisionA, $divisionB];
    }

    private function makeTaskScope(): array
    {
        $gm = $this->makeRoleUser('GMS', 'general-manager');
        $manager = $this->makeRoleUser('MANS', 'manager');
        $supervisor = $this->makeRoleUser('SUPS', 'supervisor');
        $worker = $this->makeRoleUser('WORKS', 'task-worker');
        $otherWorker = $this->makeRoleUser('WORK2', 'task-worker');

        $division = Divisi::create([
            'name' => 'Technology',
            'description' => 'Tech',
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

        $worker->update(['team_id' => $team->id]);

        return [$gm, $manager, $supervisor, $worker, $otherWorker, $team, $division];
    }

    private function makeTwoTeams(): array
    {
        $gm = $this->makeRoleUser('GMT', 'general-manager');
        $manager = $this->makeRoleUser('MANT', 'manager');
        $supervisor = $this->makeRoleUser('SUPT', 'supervisor');
        $worker = $this->makeRoleUser('WORKT', 'task-worker');
        $otherSupervisor = $this->makeRoleUser('SUP2', 'supervisor');
        $otherWorker = $this->makeRoleUser('WORK3', 'task-worker');

        $division = Divisi::create([
            'name' => 'Product',
            'description' => 'Product',
            'is_active' => 'active',
            'manager_id' => $manager->id,
        ]);

        $team = Team::create([
            'name' => 'Frontend',
            'description' => 'Frontend',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $otherTeam = Team::create([
            'name' => 'Backend',
            'description' => 'Backend',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $otherSupervisor->id,
        ]);

        $worker->update(['team_id' => $team->id]);
        $otherWorker->update(['team_id' => $otherTeam->id]);

        return [$gm, $manager, $supervisor, $worker, $otherSupervisor, $otherWorker, $team, $otherTeam, $division];
    }

    private function makeStructure(): array
    {
        $gm = $this->makeRoleUser('GM', 'general-manager');
        $manager = $this->makeRoleUser('MAN', 'manager');
        $supervisor = $this->makeRoleUser('SUP', 'supervisor');
        $worker = $this->makeRoleUser('WORK', 'task-worker');

        $division = Divisi::create([
            'name' => 'Technology',
            'description' => 'Technology',
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

        $worker->update(['team_id' => $team->id]);

        return [$gm, $manager, $supervisor, $worker, $team, $division];
    }

    private function makeRoleUser(string $suffix, string $roleName): Employees
    {
        $user = User::factory()->create([
            'name' => $roleName . ' ' . $suffix,
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        foreach ($this->permissionsForRole($roleName) as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
            $role->givePermissionTo($permission);
        }

        $user->assignRole($role);

        return Employees::create([
            'employee_code' => 'EMP-' . preg_replace('/[^A-Z0-9-]/i', '', $suffix),
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);
    }

    private function permissionsForRole(string $roleName): array
    {
        return match ($roleName) {
            'general-manager' => [
                'view-master-project','create-master-project','update-master-project','approve-master-project',
                'view-division-project','create-division-project','update-division-project','assign-project-team',
                'review-division-project','view-task','update-task',
            ],
            'manager' => [
                'view-master-project','view-division-project','assign-project-team','view-task','update-task',
                'submit-division-project-to-gm',
            ],
            'supervisor' => [
                'view-master-project','view-division-project','view-task','create-task','update-task',
            ],
            default => [
                'view-master-project','view-division-project','view-task','update-own-task',
            ],
        };
    }
}
