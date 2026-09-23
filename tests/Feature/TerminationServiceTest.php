<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\EmployeeContract;
use App\Models\EmployeeTermination;
use App\Models\EmployeeTerminationClearance;
use App\Models\Employees;
use App\Models\DivisionProject;
use App\Models\MasterProject;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Service\TerminationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use LogicException;
use Tests\TestCase;

class TerminationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Date::setTestNow();
        parent::tearDown();
    }

    private function makeEmployee(): array
    {
        $user = User::factory()->create(['status' => 'active']);

        $employee = Employees::create([
            'employee_code' => 'EMP-' . uniqid(),
            'user_id' => $user->id,
            'status_employee' => 'active',
        ]);

        $contract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-' . uniqid(),
            'position_name' => 'Backend Developer',
            'employement_type' => 'pkwtt',
            'start_date' => '2026-01-01',
            'end_date' => null,
            'salary_daily' => 100000,
            'status' => 'active',
        ]);

        return [$user, $employee, $contract];
    }

    public function test_create_termination_starts_process_directly(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        $service = app(TerminationService::class);

        $termination = $service->create(
            employee: $employee,
            initiatedBy: $user,
            effectiveDate: '2026-10-15',
            reasonType: 'performance',
            reason: 'Kinerja tidak memenuhi target yang ditetapkan.',
        );

        $this->assertSame(EmployeeTermination::STATUS_IN_PROGRESS, $termination->status);
        $this->assertSame('2026-10-15', $termination->effective_date?->format('Y-m-d'));
        $this->assertDatabaseCount('employee_termination_clearances', 6);
        $this->assertDatabaseCount('employee_termination_handover_items', 0);

        $this->assertDatabaseHas('employee_termination_clearances', [
            'termination_id' => $termination->id,
            'category' => 'work',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('employee_termination_clearances', [
            'termination_id' => $termination->id,
            'category' => 'organization',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('employee_termination_histories', [
            'termination_id' => $termination->id,
            'from_status' => null,
            'to_status' => EmployeeTermination::STATUS_IN_PROGRESS,
        ]);

        $this->assertSame('active', $employee->refresh()->status_employee);
    }

    public function test_create_termination_prevents_duplicate_active_process(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        $service = app(TerminationService::class);

        $service->create(
            employee: $employee,
            initiatedBy: $user,
            effectiveDate: '2026-10-15',
            reasonType: 'performance',
            reason: 'Pengajuan pertama.',
        );

        $this->expectException(LogicException::class);

        $service->create(
            employee: $employee->refresh(),
            initiatedBy: $user,
            effectiveDate: '2026-11-15',
            reasonType: 'disciplinary',
            reason: 'Pengajuan kedua.',
        );
    }

    public function test_cancelled_termination_keeps_employee_active_and_allows_new_process(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        $service = app(TerminationService::class);

        $first = $service->create(
            employee: $employee,
            initiatedBy: $user,
            effectiveDate: '2026-10-15',
            reasonType: 'performance',
            reason: 'Proses pertama.',
        );

        $first = $service->cancel(
            termination: $first,
            actor: $user,
            reason: 'Proses dibatalkan oleh HR.',
        );

        $this->assertSame(EmployeeTermination::STATUS_CANCELLED, $first->status);
        $this->assertSame('active', $employee->refresh()->status_employee);

        $second = $service->create(
            employee: $employee->refresh(),
            initiatedBy: $user,
            effectiveDate: '2026-11-15',
            reasonType: 'restructuring',
            reason: 'Proses kedua.',
        );

        $this->assertSame(EmployeeTermination::STATUS_IN_PROGRESS, $second->status);
    }

    public function test_clearance_actions_apply_and_cancel_restores_previous_state(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();

        $divisi = Divisi::create([
            'name' => 'Engineering ' . uniqid(),
            'description' => 'Engineering',
            'is_active' => 'active',
            'manager_id' => $employee->id,
        ]);

        $team = Team::create([
            'name' => 'Backend ' . uniqid(),
            'divisi_id' => $divisi->id,
            'description' => 'Backend',
            'is_active' => 'active',
            'supervisor_id' => $employee->id,
        ]);

        $employee->update(['team_id' => $team->id]);

        $termination = app(TerminationService::class)->create(
            employee: $employee->refresh(),
            initiatedBy: $user,
            effectiveDate: '2026-10-15',
            reasonType: 'performance',
            reason: 'Kinerja tidak memenuhi target.',
        );

        $service = app(TerminationService::class);

        $organization = EmployeeTerminationClearance::query()
            ->where('termination_id', $termination->id)
            ->where('category', 'organization')
            ->firstOrFail();

        $access = EmployeeTerminationClearance::query()
            ->where('termination_id', $termination->id)
            ->where('category', 'access')
            ->firstOrFail();

        $service->updateClearance($organization, $user, 'completed');
        $service->updateClearance($access, $user, 'completed');

        $this->assertNull($employee->refresh()->team_id);
        $this->assertNull($team->refresh()->supervisor_id);
        $this->assertNull($divisi->refresh()->manager_id);
        $this->assertSame('inactive', $user->refresh()->status);

        $service->cancel(
            termination: $termination->refresh(),
            actor: $user,
            reason: 'Proses PHK dibatalkan oleh HR.',
        );

        $this->assertSame('active', $employee->refresh()->status_employee);
        $this->assertSame($team->id, $employee->team_id);
        $this->assertSame($employee->id, $team->refresh()->supervisor_id);
        $this->assertSame($employee->id, $divisi->refresh()->manager_id);
        $this->assertSame('active', $user->refresh()->status);
    }

    public function test_handover_reassigns_task_and_cancel_restores_original_assignee(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        [, $recipient] = $this->makeEmployee();

        $divisi = Divisi::create([
            'name' => 'Product ' . uniqid(),
            'description' => 'Product',
            'is_active' => 'active',
            'manager_id' => $employee->id,
        ]);

        $team = Team::create([
            'name' => 'Product Team ' . uniqid(),
            'divisi_id' => $divisi->id,
            'description' => 'Product',
            'is_active' => 'active',
            'supervisor_id' => $employee->id,
        ]);

        $employee->update(['team_id' => $team->id]);
        $recipient->update(['team_id' => $team->id]);

        $masterProject = MasterProject::create([
            'created_by' => $employee->id,
            'name' => 'Master Project ' . uniqid(),
            'description' => 'Test',
            'status' => 'in_progress',
        ]);

        $divisionProject = DivisionProject::create([
            'master_project_id' => $masterProject->id,
            'divisi_id' => $divisi->id,
            'manager_id' => $employee->id,
            'created_by' => $employee->id,
            'name' => 'Division Project ' . uniqid(),
            'description' => 'Test',
            'status' => 'in_progress',
            'is_required' => true,
            'manual_progress' => 0,
        ]);

        $task = Task::create([
            'division_project_id' => $divisionProject->id,
            'team_id' => $team->id,
            'assignee_id' => $employee->id,
            'created_by' => $employee->id,
            'title' => 'Task handover ' . uniqid(),
            'description' => 'Test handover',
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        $termination = app(TerminationService::class)->create(
            employee: $employee->refresh(),
            initiatedBy: $user,
            effectiveDate: '2026-10-15',
            reasonType: 'restructuring',
            reason: 'Restrukturisasi organisasi.',
        );

        $item = $termination->handoverItems()->firstOrFail();

        app(TerminationService::class)->updateHandover(
            item: $item,
            verifier: $user,
            status: 'completed',
            handoverToEmployeeId: $recipient->id,
        );

        $this->assertSame($recipient->id, $task->refresh()->assignee_id);

        app(TerminationService::class)->cancel(
            termination: $termination->refresh(),
            actor: $user,
            reason: 'Proses dibatalkan.',
        );

        $this->assertSame($employee->id, $task->refresh()->assignee_id);
    }

    public function test_completion_requires_clearance_and_effective_date_but_not_payroll(): void
    {
        Date::setTestNow('2026-09-20');
        [$user, $employee, $contract] = $this->makeEmployee();

        $termination = app(TerminationService::class)->create(
            employee: $employee,
            initiatedBy: $user,
            effectiveDate: '2026-09-20',
            reasonType: 'efficiency',
            reason: 'Kebutuhan organisasi.',
        );

        EmployeeTerminationClearance::query()
            ->where('termination_id', $termination->id)
            ->where('category', 'asset')
            ->update(['status' => 'pending']);

        $this->expectException(LogicException::class);

        app(TerminationService::class)->complete(
            termination: $termination,
            actor: $user,
        );

        $this->assertSame('active', $employee->refresh()->status_employee);
        $this->assertSame('active', $contract->refresh()->status);

        $this->assertDatabaseHas('employee_termination_clearances', [
            'termination_id' => $termination->id,
            'category' => 'asset',
            'status' => 'pending',
        ]);
    }

    public function test_completion_releases_assignments_deactivates_account_and_terminates_contract(): void
    {
        Date::setTestNow('2026-01-01');
        [$user, $employee, $contract] = $this->makeEmployee();

        $divisi = Divisi::create([
            'name' => 'Engineering ' . uniqid(),
            'description' => 'Engineering',
            'is_active' => 'active',
        ]);

        $team = Team::create([
            'name' => 'Backend ' . uniqid(),
            'divisi_id' => $divisi->id,
            'description' => 'Backend',
            'is_active' => 'active',
            'supervisor_id' => $employee->id,
        ]);

        $employee->update([
            'team_id' => $team->id,
        ]);

        $divisi->update([
            'manager_id' => $employee->id,
        ]);

        $termination = app(TerminationService::class)->create(
            employee: $employee->refresh(),
            initiatedBy: $user,
            effectiveDate: '2026-01-01',
            reasonType: 'disciplinary',
            reason: 'Pelanggaran disiplin.',
        );

        EmployeeTerminationClearance::query()
            ->where('termination_id', $termination->id)
            ->update([
                'status' => 'completed',
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

        $completed = app(TerminationService::class)->complete(
            termination: $termination,
            actor: $user,
        );

        $this->assertSame(EmployeeTermination::STATUS_COMPLETED, $completed->status);

        $employee = $employee->refresh();

        $this->assertSame('terminated', $employee->status_employee);
        $this->assertSame('2026-01-01', optional($employee->TerminationDate)?->format('Y-m-d'));
        $this->assertNull($employee->team_id);
        $this->assertNull($team->refresh()->supervisor_id);
        $this->assertNull($divisi->refresh()->manager_id);
        $this->assertSame('inactive', $user->refresh()->status);
        $this->assertSame('terminated', $contract->refresh()->status);

        $this->assertDatabaseHas('employee_status_histories', [
            'employee_id' => $employee->id,
            'old_status' => 'active',
            'new_status' => 'terminated',
            'effective_date' => '2026-01-01',
        ]);

        $this->assertDatabaseHas('employee_termination_histories', [
            'termination_id' => $termination->id,
            'from_status' => EmployeeTermination::STATUS_IN_PROGRESS,
            'to_status' => EmployeeTermination::STATUS_COMPLETED,
        ]);
    }
}
