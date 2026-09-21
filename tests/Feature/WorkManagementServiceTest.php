<?php

namespace Tests\Feature;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Service\WorkManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_work_management_flow_reaches_master_final_approval(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject(
            $generalManager,
            'Website Redesign',
            'Company-wide website project',
        );

        $divisionProject = $service->createDivisionProject(
            $master,
            $division,
            $generalManager,
            'Engineering Delivery',
        );

        $service->assignTeam($divisionProject, $team, $manager);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Build API',
            'Implement the backend API',
        );

        $service->reportManualProgress($divisionProject, $supervisor, 75, 'Manual checkpoint.');

        $service->updateTaskWork(
            $task,
            $worker,
            100,
            'API completed.',
            null,
            Task::STATUS_IN_PROGRESS,
        );
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'approved', 'Good.');

        $this->assertSame(Task::STATUS_DONE, $task->refresh()->status);
        $this->assertSame(100, $divisionProject->refresh()->automaticProgress());
        $this->assertSame(75, $divisionProject->manual_progress);
        $this->assertSame('ready_for_review', $divisionProject->status);

        $service->reviewDivisionProject($divisionProject, $manager, 'approved', 'Division accepted.');

        $this->assertSame('completed', $divisionProject->refresh()->status);
        $this->assertSame('ready_for_review', $master->refresh()->status);

        $service->reviewMasterProject($master, $generalManager, 'approved', 'Final approval.');

        $master->refresh();

        $this->assertSame('completed', $master->status);
        $this->assertSame($generalManager->id, $master->approved_by);
        $this->assertNotNull($master->approved_at);

        $this->assertDatabaseHas('project_reviews', [
            'master_project_id' => $master->id,
            'reviewer_id' => $generalManager->id,
            'reviewer_level' => 'general_manager',
            'decision' => 'approved',
        ]);

        $this->assertGreaterThanOrEqual(5, \App\Models\WorkManagementAudit::query()->count());
    }

    public function test_task_can_be_blocked_and_rejected_for_revision(): void
    {
        [$generalManager, , $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $generalManager);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Blocked task',
        );

        $service->updateTaskWork($task, $worker, 40, null, 'Waiting for approval.', Task::STATUS_BLOCKED);

        $task->refresh();

        $this->assertSame(Task::STATUS_BLOCKED, $task->status);
        $this->assertSame(40, $task->progress);

        $service->updateTaskWork($task, $worker, 100, 'Finished', null, Task::STATUS_IN_PROGRESS);
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'rejected', 'Please fix edge cases.');

        $task->refresh();

        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(99, $task->progress);
        $this->assertNull($task->completed_at);
    }

    public function test_worker_cannot_update_another_workers_task(): void
    {
        [$generalManager, , $supervisor, $worker, $team, $division] = $this->makeStructure();
        $otherUser = User::factory()->create();
        $otherWorker = $this->makeEmployee('OTHER', $otherUser);

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $generalManager);
        $task = $service->createTask($divisionProject, $team, $worker, $supervisor, 'Owned task');

        $this->expectException(ValidationException::class);

        $service->updateTaskWork($task, $otherWorker, 50, 'No access', null, Task::STATUS_IN_PROGRESS);
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
        $user = User::factory()->create(['name' => $roleName . ' ' . $suffix]);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        foreach ($this->permissionsForRole($roleName) as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            $role->givePermissionTo($permission);
        }

        $user->assignRole($role);

        return $this->makeEmployee($suffix, $user);
    }

    private function permissionsForRole(string $roleName): array
    {
        return match ($roleName) {
            'general-manager' => [
                'view-master-project','create-master-project','update-master-project','approve-master-project',
                'view-division-project','create-division-project','update-division-project','assign-project-team',
                'report-project-progress','review-division-project','view-task','create-task','assign-task',
                'update-task','update-own-task','submit-task','review-task',
            ],
            'manager' => [
                'view-master-project','view-division-project','update-division-project','assign-project-team',
                'report-project-progress','review-division-project','view-task','assign-task','update-task',
            ],
            'supervisor' => [
                'view-master-project','view-division-project','report-project-progress','view-task',
                'create-task','assign-task','update-task','review-task',
            ],
            default => ['view-master-project','view-division-project','view-task','update-own-task','submit-task'],
        };
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
