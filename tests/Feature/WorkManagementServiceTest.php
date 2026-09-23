<?php

namespace Tests\Feature;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\MasterProject;
use App\Models\ProjectReport;
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

        $service->reportManualProgress($divisionProject, $manager, 75, 'Manual checkpoint from Manager.');

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

        $service->submitSupervisorReport(
            $divisionProject,
            $supervisor,
            'Semua task Backend selesai dan hasil sudah diverifikasi.',
        );

        $this->assertSame('submitted_to_manager', $divisionProject->refresh()->status);
        $this->assertDatabaseHas('project_reports', [
            'division_project_id' => $divisionProject->id,
            'team_id' => $team->id,
            'reported_by' => $supervisor->id,
            'report_level' => ProjectReport::LEVEL_SUPERVISOR,
            'status' => ProjectReport::STATUS_SUBMITTED,
        ]);

        $service->reviewTeamReport($divisionProject, $team, $manager, 'approved', 'Laporan Team sudah sesuai.');

        $this->assertSame('manager_approved', $divisionProject->refresh()->status);

        $service->submitDivisionProjectToGM(
            $divisionProject,
            $manager,
            'Division Project telah direview. Seluruh pekerjaan Team telah selesai.',
        );

        $this->assertSame('submitted_to_gm', $divisionProject->refresh()->status);
        $this->assertSame('ready_for_review', $master->refresh()->status);

        $this->assertDatabaseHas('project_reports', [
            'division_project_id' => $divisionProject->id,
            'team_id' => null,
            'reported_by' => $manager->id,
            'report_level' => ProjectReport::LEVEL_MANAGER,
            'status' => ProjectReport::STATUS_SUBMITTED,
        ]);

        $service->reviewMasterProject($master, $generalManager, 'approved', 'Final approval.');

        $master->refresh();

        $this->assertSame('completed', $master->status);
        $this->assertSame('completed', $divisionProject->refresh()->status);
        $this->assertSame($generalManager->id, $master->approved_by);
        $this->assertNotNull($master->approved_at);

        $this->assertDatabaseHas('project_reviews', [
            'master_project_id' => $master->id,
            'reviewer_id' => $generalManager->id,
            'reviewer_level' => 'general_manager',
            'decision' => 'approved',
        ]);

        $this->assertDatabaseHas('project_reports', [
            'division_project_id' => $divisionProject->id,
            'reported_by' => $manager->id,
            'report_level' => ProjectReport::LEVEL_MANAGER,
            'status' => ProjectReport::STATUS_APPROVED,
        ]);

        $this->assertGreaterThanOrEqual(5, \App\Models\WorkManagementAudit::query()->count());
    }

    public function test_gm_can_return_master_project_and_manager_can_resubmit_to_gm(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $otherGeneralManager = $this->makeRoleUser('GM2', 'general-manager');

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $manager);

        $task = $service->createTask($divisionProject, $team, $worker, $supervisor, 'Completed task');
        $service->updateTaskWork($task, $worker, 100, 'Done', null, Task::STATUS_IN_PROGRESS);
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'approved');

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Team selesai.');
        $service->reviewTeamReport($divisionProject, $team, $manager, 'approved', 'OK.');
        $service->submitDivisionProjectToGM($divisionProject, $manager, 'Laporan awal Manager.');

        $this->assertSame('ready_for_review', $master->refresh()->status);

        $this->expectException(ValidationException::class);
        $service->reviewMasterProject($master, $otherGeneralManager, 'approved');
    }

    public function test_supervisor_cannot_report_division_manual_progress(): void
    {
        [$generalManager, $manager, $supervisor, , $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $manager);

        $this->expectException(ValidationException::class);

        $service->reportManualProgress($divisionProject, $supervisor, 50, 'Supervisor progress.');
    }

    public function test_manager_waits_for_all_supervisor_reports_before_review(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();

        $secondSupervisor = $this->makeRoleUser('SUP2', 'supervisor');
        $secondWorker = $this->makeRoleUser('WORK2', 'task-worker');

        $secondTeam = Team::create([
            'name' => 'Frontend',
            'description' => 'Frontend',
            'is_active' => 'active',
            'divisi_id' => $division->id,
            'supervisor_id' => $secondSupervisor->id,
        ]);

        $secondWorker->update(['team_id' => $secondTeam->id]);

        $service = app(WorkManagementService::class);
        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');

        $service->assignTeam($divisionProject, $team, $manager);
        $service->assignTeam($divisionProject, $secondTeam, $manager);

        foreach ([
            [$worker, $supervisor, $team, 'Backend task'],
            [$secondWorker, $secondSupervisor, $secondTeam, 'Frontend task'],
        ] as [$taskWorker, $taskSupervisor, $assignedTeam, $title]) {
            $task = $service->createTask(
                $divisionProject,
                $assignedTeam,
                $taskWorker,
                $taskSupervisor,
                $title,
            );

            $service->updateTaskWork($task, $taskWorker, 100, 'Done', null, Task::STATUS_IN_PROGRESS);
            $service->submitTask($task, $taskWorker);
            $service->reviewTask($task, $taskSupervisor, 'approved');
        }

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Backend selesai.');

        $this->assertSame('in_progress', $divisionProject->refresh()->status);

        $this->expectException(ValidationException::class);
        $service->reviewTeamReport($divisionProject, $team, $manager, 'approved');
    }

    public function test_gm_rejection_returns_submitted_division_projects_for_manager_revision(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $manager);

        $task = $service->createTask($divisionProject, $team, $worker, $supervisor, 'Completed task');
        $service->updateTaskWork($task, $worker, 100, 'Done', null, Task::STATUS_IN_PROGRESS);
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'approved');

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Team selesai.');
        $service->reviewTeamReport($divisionProject, $team, $manager, 'approved');
        $service->submitDivisionProjectToGM($divisionProject, $manager, 'Laporan awal Manager.');

        $service->reviewMasterProject($master, $generalManager, 'rejected', 'Tambahkan detail hasil akhir dan kendala.');

        $this->assertSame('in_progress', $master->refresh()->status);
        $this->assertSame('revision_required', $divisionProject->refresh()->status);
        $this->assertDatabaseHas('project_reports', [
            'division_project_id' => $divisionProject->id,
            'report_level' => ProjectReport::LEVEL_MANAGER,
            'status' => ProjectReport::STATUS_REJECTED,
        ]);

        $service->submitDivisionProjectToGM($divisionProject, $manager, 'Laporan Manager yang sudah diperbaiki.');

        $this->assertSame('submitted_to_gm', $divisionProject->refresh()->status);
        $this->assertSame('ready_for_review', $master->refresh()->status);

        $service->reviewMasterProject($master, $generalManager, 'approved', 'Sudah sesuai.');

        $this->assertSame('completed', $master->refresh()->status);
        $this->assertSame('completed', $divisionProject->refresh()->status);
    }

    public function test_manager_can_update_manual_progress_until_submitted_to_gm(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $manager);

        $task = $service->createTask($divisionProject, $team, $worker, $supervisor, 'Completed task');
        $service->updateTaskWork($task, $worker, 100, 'Done', null, Task::STATUS_IN_PROGRESS);
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'approved');

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Team selesai.');

        $this->assertSame('submitted_to_manager', $divisionProject->refresh()->status);

        $service->reportManualProgress($divisionProject, $manager, 100, 'Progress final Manager.');

        $this->assertSame(100, $divisionProject->refresh()->manual_progress);

        $service->reviewTeamReport($divisionProject, $team, $manager, 'approved', 'OK.');
        $this->assertSame('manager_approved', $divisionProject->refresh()->status);

        $service->reportManualProgress($divisionProject, $manager, 90, 'Koreksi progress sebelum dikirim ke GM.');

        $this->assertSame(90, $divisionProject->refresh()->manual_progress);
    }

    public function test_manager_can_reject_supervisor_report_for_revision(): void
    {
        [$generalManager, $manager, $supervisor, $worker, $team, $division] = $this->makeStructure();
        $service = app(WorkManagementService::class);

        $master = $service->createMasterProject($generalManager, 'Project');
        $divisionProject = $service->createDivisionProject($master, $division, $generalManager, 'Division');
        $service->assignTeam($divisionProject, $team, $manager);

        $task = $service->createTask(
            $divisionProject,
            $team,
            $worker,
            $supervisor,
            'Completed task',
        );

        $service->updateTaskWork($task, $worker, 100, 'Done', null, Task::STATUS_IN_PROGRESS);
        $service->submitTask($task, $worker);
        $service->reviewTask($task, $supervisor, 'approved');

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Draft report.');
        $service->reviewTeamReport($divisionProject, $team, $manager, 'rejected', 'Tambahkan detail hasil dan kendala.');

        $this->assertSame('revision_required', $divisionProject->refresh()->status);
        $this->assertDatabaseHas('project_reviews', [
            'division_project_id' => $divisionProject->id,
            'reviewer_id' => $manager->id,
            'reviewer_level' => 'manager',
            'decision' => 'rejected',
        ]);
        $this->assertDatabaseHas('project_reports', [
            'division_project_id' => $divisionProject->id,
            'team_id' => $team->id,
            'report_level' => ProjectReport::LEVEL_SUPERVISOR,
            'status' => ProjectReport::STATUS_REJECTED,
        ]);

        $service->submitSupervisorReport($divisionProject, $supervisor, 'Revised report with complete results.');
        $this->assertSame('submitted_to_manager', $divisionProject->refresh()->status);
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
        $user = User::factory()->create(['name' => $roleName . ' ' . $suffix, 'status' => 'active']);
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
                'report-project-progress','submit-division-project-report','review-division-project','submit-division-project-to-gm','view-task','create-task','assign-task',
                'update-task','update-own-task','submit-task','review-task',
            ],
            'manager' => [
                'view-master-project','view-division-project','update-division-project','assign-project-team',
                'report-project-progress','review-division-project','submit-division-project-to-gm','view-task','assign-task','update-task',
            ],
            'supervisor' => [
                'view-master-project','view-division-project','report-project-progress','submit-division-project-report','view-task',
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
