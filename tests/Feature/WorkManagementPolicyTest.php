<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\Task;
use App\Livewire\Page\Main\WorkManagement\DivisionProjectTeamDetail;
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

    public function test_manager_can_only_view_division_projects_they_manage(): void
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

    public function test_task_worker_can_only_view_tasks_assigned_to_them(): void
    {
        [$gm, $manager, $supervisor, $worker, $otherWorker, $team, $division] = $this->makeTaskScope();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project');
        $service->assignTeam($project, $team, $manager);

        $task = $service->createTask($project, $team, $worker, $supervisor, 'Owned task');

        $this->assertTrue(Gate::forUser($worker->user)->allows('view', $task));
        $this->assertFalse(Gate::forUser($otherWorker->user)->allows('view', $task));
    }

    public function test_supervisor_can_review_only_their_team_tasks(): void
    {
        [$gm, $manager, $supervisor, $worker, $otherSupervisor, $otherWorker, $team, $otherTeam, $division] = $this->makeTwoTeams();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project');
        $service->assignTeam($project, $team, $manager);
        $service->assignTeam($project, $otherTeam, $manager);

        $task = $service->createTask($project, $team, $worker, $supervisor, 'Team A task');

        $this->assertTrue(Gate::forUser($supervisor->user)->allows('review', $task));
        $this->assertFalse(Gate::forUser($otherSupervisor->user)->allows('review', $task));

        $this->assertNotNull($otherWorker->team);
    }

    public function test_supervisor_can_only_view_their_assigned_project_team_page(): void
    {
        [$gm, $manager, $supervisor, $worker, $otherSupervisor, $otherWorker, $team, $otherTeam, $division] = $this->makeTwoTeams();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Master');
        $project = $service->createDivisionProject($master, $division, $gm, 'Project');
        $service->assignTeam($project, $team, $manager);
        $service->assignTeam($project, $otherTeam, $manager);

        $this->assertTrue(Gate::forUser($supervisor->user)->allows('viewTeam', [$project, $team]));
        $this->assertFalse(Gate::forUser($supervisor->user)->allows('viewTeam', [$project, $otherTeam]));
        $this->assertTrue(Gate::forUser($manager->user)->allows('viewTeam', [$project, $otherTeam]));
    }

    private function makeManagers(): array
    {
        $gm = $this->makeRoleUser('GM1', 'general-manager');
        $managerA = $this->makeRoleUser('MAN1', 'manager');
        $managerB = $this->makeRoleUser('MAN2', 'manager');

        $divisionA = Divisi::create(['name' => 'Division A', 'description' => 'A', 'is_active' => 'active', 'manager_id' => $managerA->id]);
        $divisionB = Divisi::create(['name' => 'Division B', 'description' => 'B', 'is_active' => 'active', 'manager_id' => $managerB->id]);

        return [$managerA, $managerB, $gm, $divisionA, $divisionB];
    }

    private function makeTaskScope(): array
    {
        $gm = $this->makeRoleUser('GMS', 'general-manager');
        $manager = $this->makeRoleUser('MANS', 'manager');
        $supervisor = $this->makeRoleUser('SUPS', 'supervisor');
        $worker = $this->makeRoleUser('WORKS', 'task-worker');
        $otherWorker = $this->makeRoleUser('WORK2', 'task-worker');

        $division = Divisi::create(['name' => 'Technology', 'description' => 'Tech', 'is_active' => 'active', 'manager_id' => $manager->id]);
        $team = Team::create(['name' => 'Backend', 'description' => 'Backend', 'is_active' => 'active', 'divisi_id' => $division->id, 'supervisor_id' => $supervisor->id]);
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

        $division = Divisi::create(['name' => 'Product', 'description' => 'Product', 'is_active' => 'active', 'manager_id' => $manager->id]);

        $team = Team::create(['name' => 'Frontend', 'description' => 'Frontend', 'is_active' => 'active', 'divisi_id' => $division->id, 'supervisor_id' => $supervisor->id]);
        $otherTeam = Team::create(['name' => 'QA', 'description' => 'QA', 'is_active' => 'active', 'divisi_id' => $division->id, 'supervisor_id' => $otherSupervisor->id]);

        $worker->update(['team_id' => $team->id]);
        $otherWorker->update(['team_id' => $otherTeam->id]);

        return [$gm, $manager, $supervisor, $worker, $otherSupervisor, $otherWorker, $team, $otherTeam, $division];
    }

    private function makeRoleUser(string $suffix, string $roleName): Employees
    {
        $user = User::factory()->create(['name' => $roleName . ' ' . $suffix]);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $permissions = match ($roleName) {
            'general-manager' => ['view-master-project','create-master-project','approve-master-project','view-division-project','create-division-project','update-division-project','assign-project-team','report-project-progress','review-division-project','view-task','create-task','assign-task','update-task','update-own-task','submit-task','review-task'],
            'manager' => ['view-master-project','view-division-project','update-division-project','assign-project-team','report-project-progress','review-division-project','view-task','assign-task','update-task'],
            'supervisor' => ['view-master-project','view-division-project','report-project-progress','view-task','create-task','assign-task','update-task','review-task'],
            default => ['view-master-project','view-division-project','view-task','update-own-task','submit-task'],
        };

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
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
}
