<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Employees;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Service\WorkManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WorkManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_gm_can_create_master_and_division_project_with_initial_team(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($gm, 'Website Company');
        $divisionProject = $service->createDivisionProject(
            $master,
            $division,
            $gm,
            'Backend Company Website',
            'API dan database',
            null,
            null,
            true,
            $team,
        );

        $this->assertSame('in_progress', $master->refresh()->status);
        $this->assertSame($division->id, $divisionProject->division->id);
        $this->assertSame($manager->id, $divisionProject->manager_id);
        $this->assertTrue(
            $divisionProject->teams()->whereKey($team->id)->exists()
        );
    }

    public function test_only_supervisor_of_selected_team_can_create_task(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        [$otherSupervisor, , $otherTeam] = $this->makeAdditionalTeam($division, 'Frontend');

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $gm, 'Division', initialTeam: $team);

        $this->expectException(ValidationException::class);

        $service->createTask(
            $divisionProject,
            $otherTeam,
            $worker,
            $otherSupervisor,
            'Invalid task',
        );
    }

    public function test_task_worker_completes_task_with_checkbox_and_can_reopen_it(): void
    {
        [$gm, , $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $gm, 'Division', initialTeam: $team);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Build API',
        );

        $service->toggleTaskCompletion($task, $worker, true);

        $task->refresh();

        $this->assertSame(Task::STATUS_DONE, $task->status);
        $this->assertSame(100, $task->progress);
        $this->assertNotNull($task->completed_at);
        $this->assertNull($task->submitted_at);

        $service->toggleTaskCompletion($task, $worker, false);

        $task->refresh();

        $this->assertSame(Task::STATUS_TO_DO, $task->status);
        $this->assertSame(0, $task->progress);
        $this->assertNull($task->completed_at);
    }

    public function test_task_worker_cannot_change_task_after_division_is_submitted_to_gm(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $gm, 'Division', initialTeam: $team);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Build API',
        );

        $service->toggleTaskCompletion($task, $worker, true);
        $service->submitDivisionProjectForCompletion($divisionProject, $manager);

        $this->expectException(ValidationException::class);

        $service->toggleTaskCompletion($task, $worker, false);
    }

    public function test_manager_can_submit_division_project_only_when_all_tasks_are_done(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $gm, 'Division', initialTeam: $team);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Build API',
        );

        try {
            $service->submitDivisionProjectForCompletion($divisionProject, $manager);
            $this->fail('Expected validation exception when task is still open.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('division_project', $exception->errors());
        }

        $service->toggleTaskCompletion($task, $worker, true);
        $service->submitDivisionProjectForCompletion($divisionProject, $manager);

        $this->assertSame('submitted_to_gm', $divisionProject->refresh()->status);
    }

    public function test_gm_can_complete_division_and_reopen_it_for_revision(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $gm, 'Division', initialTeam: $team);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Build API',
        );

        $service->toggleTaskCompletion($task, $worker, true);
        $service->submitDivisionProjectForCompletion($divisionProject, $manager);

        $service->reviewDivisionProjectCompletion(
            $divisionProject,
            $gm,
            'approved',
        );

        $this->assertSame('completed', $divisionProject->refresh()->status);

        $service->reviewDivisionProjectCompletion(
            $divisionProject,
            $gm,
            'rejected',
            'Tambahkan validasi error handling.',
        );

        $this->assertSame('revision_required', $divisionProject->refresh()->status);
        $this->assertSame('in_progress', $master->refresh()->status);
    }

    public function test_master_project_can_be_completed_only_after_all_divisions_are_complete(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        [$secondSupervisor, $secondWorker, $secondTeam, $secondDivision] = $this->makeAdditionalStructure('Frontend');

        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($gm, 'Company Portal');

        $backend = $service->createDivisionProject(
            $master,
            $division,
            $gm,
            'Backend',
            initialTeam: $team,
        );

        $frontend = $service->createDivisionProject(
            $master,
            $secondDivision,
            $gm,
            'Frontend',
            initialTeam: $secondTeam,
        );

        $backendTask = $service->createTask($backend, $team, $worker, $supervisor, 'Backend task');
        $frontendTask = $service->createTask($frontend, $secondTeam, $secondWorker, $secondSupervisor, 'Frontend task');

        $service->toggleTaskCompletion($backendTask, $worker, true);
        $service->submitDivisionProjectForCompletion($backend, $manager);
        $service->reviewDivisionProjectCompletion($backend, $gm, 'approved');

        try {
            $service->completeMasterProject($master, $gm);
            $this->fail('Expected validation exception while a division is incomplete.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('master_project', $exception->errors());
        }

        $secondManager = $secondDivision->manager;
        $service->toggleTaskCompletion($frontendTask, $secondWorker, true);
        $service->submitDivisionProjectForCompletion($frontend, $secondManager);
        $service->reviewDivisionProjectCompletion($frontend, $gm, 'approved');

        $service->completeMasterProject($master, $gm);

        $this->assertSame('completed', $master->refresh()->status);
    }

    public function test_non_gm_cannot_create_master_project(): void
    {
        [$gm, $manager] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $this->expectException(ValidationException::class);
        $service->createMasterProject($manager, 'Not allowed');
    }

    public function test_non_supervisor_cannot_create_task(): void
    {
        [$gm, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($gm, 'Allowed');
        $divisionProject = $service->createDivisionProject(
            $master,
            $division,
            $gm,
            'Division',
            initialTeam: $team,
        );

        $this->expectException(ValidationException::class);
        $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $worker,
            'Not allowed task',
        );
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

    private function makeAdditionalTeam(Divisi $division, string $teamName): array
    {
        $supervisor = $this->makeRoleUser('SUP-' . $teamName, 'supervisor');
        $worker = $this->makeRoleUser('WORK-' . $teamName, 'task-worker');

        $team = Team::create([
            'name' => $teamName,
            'description' => $teamName,
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $worker->update(['team_id' => $team->id]);

        return [$supervisor, $worker, $team];
    }

    private function makeAdditionalStructure(string $name): array
    {
        $manager = $this->makeRoleUser('MAN-' . $name, 'manager');
        $supervisor = $this->makeRoleUser('SUP-' . $name, 'supervisor');
        $worker = $this->makeRoleUser('WORK-' . $name, 'task-worker');

        $division = Divisi::create([
            'name' => $name,
            'description' => $name,
            'is_active' => 'active',
            'manager_id' => $manager->id,
        ]);

        $team = Team::create([
            'name' => $name . ' Team',
            'description' => $name,
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $worker->update(['team_id' => $team->id]);

        return [$supervisor, $worker, $team, $division, $manager];
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
                'view-master-project',
                'create-master-project',
                'approve-master-project',
                'view-division-project',
                'create-division-project',
                'assign-project-team',
                'review-division-project',
                'view-task',
            ],
            'manager' => [
                'view-master-project',
                'view-division-project',
                'assign-project-team',
                'view-task',
                'submit-division-project-to-gm',
            ],
            'supervisor' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'create-task',
                'update-task',
            ],
            default => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
            ],
        };
    }
}
