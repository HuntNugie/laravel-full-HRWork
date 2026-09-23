<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\EmployeeContract;
use App\Models\EmployeeTermination;
use App\Models\EmployeeTerminationClearance;
use App\Models\Employees;
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
