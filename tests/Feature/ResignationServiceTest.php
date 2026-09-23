<?php

namespace Tests\Feature;

use App\Models\EmployeeContract;
use App\Models\EmployeeResignation;
use App\Models\EmployeeResignationClearance;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Service\ResignationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use LogicException;
use Tests\TestCase;

class ResignationServiceTest extends TestCase
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

    public function test_create_resignation_requires_active_employee_and_prevents_duplicate_active_process(): void
    {
        [$user, $employee, $contract] = $this->makeEmployee();
        $service = app(ResignationService::class);

        $resignation = $service->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Pindah pekerjaan.',
        );

        $this->assertSame(EmployeeResignation::STATUS_SUBMITTED, $resignation->status);
        $this->assertSame($contract->id, $resignation->employee_contract_id);

        $this->expectException(LogicException::class);

        $service->create(
            employee: $employee->refresh(),
            submittedBy: $user,
            proposedLastWorkingDate: '2026-11-15',
            reason: 'Pengajuan kedua.',
        );
    }

    public function test_approve_creates_clearances_and_handover_items(): void
    {
        [$user, $employee] = $this->makeEmployee();
        $resignation = app(ResignationService::class)->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Alasan.',
        );

        $approved = app(ResignationService::class)->approve(
            resignation: $resignation,
            reviewer: $user,
            approvedLastWorkingDate: '2026-10-15',
        );

        $this->assertSame(EmployeeResignation::STATUS_APPROVED, $approved->status);
        $this->assertDatabaseCount('employee_resignation_clearances', 6);
        $this->assertDatabaseCount('employee_resignation_handover_items', 0);
        $this->assertDatabaseHas('employee_resignation_clearances', [
            'resignation_id' => $resignation->id,
            'category' => 'work',
            'status' => 'completed',
        ]);
    }

    public function test_reject_records_reason_without_changing_employee_status(): void
    {
        [$user, $employee] = $this->makeEmployee();

        $resignation = app(ResignationService::class)->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Alasan.',
        );

        $rejected = app(ResignationService::class)->reject(
            resignation: $resignation,
            reviewer: $user,
            reason: 'Belum dapat diproses.',
        );

        $this->assertSame(EmployeeResignation::STATUS_REJECTED, $rejected->status);
        $this->assertSame('active', $employee->refresh()->status_employee);
        $this->assertDatabaseHas('employee_resignation_histories', [
            'resignation_id' => $resignation->id,
            'to_status' => 'rejected',
        ]);
    }

    public function test_completion_requires_paid_final_payroll_and_clearance(): void
    {
        Date::setTestNow('2026-09-20');
        [$user, $employee, $contract] = $this->makeEmployee();

        $resignation = app(ResignationService::class)->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-09-20',
            reason: 'Alasan.',
        );

        $resignation = app(ResignationService::class)->approve(
            resignation: $resignation,
            reviewer: $user,
            approvedLastWorkingDate: '2026-09-20',
        );

        $this->expectException(LogicException::class);
        app(ResignationService::class)->complete(
            resignation: $resignation,
            actor: $user,
        );

        $this->assertSame('active', $employee->refresh()->status_employee);
        $this->assertSame('active', $contract->refresh()->status);

        $this->assertDatabaseHas('employee_resignation_clearances', [
            'resignation_id' => $resignation->id,
            'category' => 'asset',
            'status' => 'pending',
        ]);
    }

    public function test_completion_updates_employee_user_contract_and_status_history(): void
    {
        Date::setTestNow('2026-01-01');
        [$user, $employee, $contract] = $this->makeEmployee();

        $resignation = app(ResignationService::class)->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-01-01',
            reason: 'Alasan.',
        );

        $resignation = app(ResignationService::class)->approve(
            resignation: $resignation,
            reviewer: $user,
            approvedLastWorkingDate: '2026-01-01',
        );

        EmployeeResignationClearance::query()
            ->where('resignation_id', $resignation->id)
            ->update([
                'status' => 'completed',
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

        $period = PayrollPeriod::create([
            'name' => 'January 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'status' => 'paid',
            'created_by' => $user->id,
            'paid_at' => now(),
        ]);

        $payroll = Payroll::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'employee_contract_id' => $contract->id,
            'position_name' => 'Backend Developer',
            'salary_daily' => 100000,
            'status' => 'paid',
            'net_amount' => 100000,
        ]);

        app(ResignationService::class)->linkFinalPayroll(
            resignation: $resignation,
            payroll: $payroll,
            actor: $user,
        );

        $completed = app(ResignationService::class)->complete(
            resignation: $resignation,
            actor: $user,
        );

        $this->assertSame(EmployeeResignation::STATUS_COMPLETED, $completed->status);
        $employee = $employee->refresh();

        $this->assertSame('resign', $employee->status_employee);
        $this->assertSame('2026-01-01', optional($employee->ResignDate)?->format('Y-m-d'));
        $this->assertSame('inactive', $user->refresh()->status);
        $this->assertSame('terminated', $contract->refresh()->status);

        $this->assertDatabaseHas('employee_status_histories', [
            'employee_id' => $employee->id,
            'old_status' => 'active',
            'new_status' => 'resign',
            'effective_date' => '2026-01-01',
        ]);
    }
}
