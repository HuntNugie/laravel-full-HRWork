<?php

namespace Tests\Feature;

use App\Models\EmployeeContract;
use App\Models\EmployeeResignation;
use App\Models\EmployeeResignationClearance;
use App\Models\Employees;
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

    public function test_create_resignation_prevents_duplicate_active_process(): void
    {
        Date::setTestNow('2026-09-23');
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
            reason: 'Pengajuan kedua saat proses pertama masih berjalan.',
        );
    }

    public function test_rejected_resignation_can_be_resubmitted_and_rejected_again(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        $service = app(ResignationService::class);

        $first = $service->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Pengajuan pertama.',
        );

        $first = $service->reject(
            resignation: $first,
            reviewer: $user,
            reason: 'Belum dapat diproses saat ini.',
        );

        $this->assertSame(EmployeeResignation::STATUS_REJECTED, $first->status);
        $this->assertSame('active', $employee->refresh()->status_employee);

        $second = $service->create(
            employee: $employee->refresh(),
            submittedBy: $user,
            proposedLastWorkingDate: '2026-11-15',
            reason: 'Pengajuan kedua setelah pengajuan pertama ditolak.',
        );

        $second = $service->reject(
            resignation: $second,
            reviewer: $user,
            reason: 'Masih perlu dipertimbangkan.',
        );

        $this->assertSame(EmployeeResignation::STATUS_REJECTED, $second->status);
        $this->assertSame(2, EmployeeResignation::query()
            ->where('employee_id', $employee->id)
            ->count());
        $this->assertDatabaseHas('employee_resignation_histories', [
            'resignation_id' => $second->id,
            'to_status' => 'rejected',
            'note' => 'Masih perlu dipertimbangkan.',
        ]);
    }

    public function test_approve_creates_clearances_and_handover_items(): void
    {
        Date::setTestNow('2026-09-23');
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
        );

        $this->assertSame(EmployeeResignation::STATUS_APPROVED, $approved->status);
        $this->assertDatabaseCount('employee_resignation_clearances', 5);
        $this->assertDatabaseCount('employee_resignation_handover_items', 0);
        $this->assertDatabaseHas('employee_resignation_clearances', [
            'resignation_id' => $resignation->id,
            'category' => 'work',
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('employee_resignation_clearances', [
            'resignation_id' => $resignation->id,
            'category' => 'organization',
        ]);
    }

    public function test_reject_records_reason_without_changing_employee_status(): void
    {
        Date::setTestNow('2026-09-23');
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

    public function test_clearance_actions_apply_and_cancel_restores_previous_state(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();

        $resignation = app(ResignationService::class)->create(
            employee: $employee->refresh(),
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Pindah pekerjaan.',
        );

        $resignation = app(ResignationService::class)->approve(
            resignation: $resignation,
            reviewer: $user,
        );

        $service = app(ResignationService::class);

        $access = EmployeeResignationClearance::query()
            ->where('resignation_id', $resignation->id)
            ->where('category', 'access')
            ->firstOrFail();

        $service->updateClearance($access, $user, 'completed');

        $this->assertSame('inactive', $user->refresh()->status);

        $service->cancel(
            resignation: $resignation->refresh(),
            actor: $user,
        );

        $this->assertSame('active', $employee->refresh()->status_employee);
        $this->assertSame('active', $user->refresh()->status);
    }

    public function test_hr_can_create_and_complete_manual_resignation_handover(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        [, $recipient] = $this->makeEmployee();
        $service = app(ResignationService::class);

        $resignation = $service->create(
            employee: $employee,
            submittedBy: $user,
            proposedLastWorkingDate: '2026-10-15',
            reason: 'Pindah pekerjaan.',
        );
        $resignation = $service->approve(
            resignation: $resignation,
            reviewer: $user,
        );

        $item = $service->createHandoverItem(
            resignation: $resignation,
            title: 'Serah terima dokumen operasional',
            description: 'Dokumentasi kerja yang masih berjalan.',
        );

        $this->assertDatabaseHas('employee_resignation_handover_items', [
            'id' => $item->id,
            'title' => 'Serah terima dokumen operasional',
            'status' => 'pending',
        ]);

        $service->updateHandover(
            item: $item,
            verifier: $user,
            status: 'completed',
            handoverToEmployeeId: $recipient->id,
            notes: 'Dokumen sudah diserahkan.',
        );

        $this->assertDatabaseHas('employee_resignation_handover_items', [
            'id' => $item->id,
            'status' => 'completed',
            'handover_to_employee_id' => $recipient->id,
            'notes' => 'Dokumen sudah diserahkan.',
        ]);

        $service->cancel(resignation: $resignation->refresh(), actor: $user);
        $this->assertSame('completed', $item->refresh()->status);
    }

    public function test_resignation_handover_requires_recipient_before_completion(): void
    {
        Date::setTestNow('2026-09-23');
        [$user, $employee] = $this->makeEmployee();
        $service = app(ResignationService::class);
        $resignation = $service->create($employee, $user, '2026-10-15', 'Alasan.');
        $resignation = $service->approve($resignation, $user);
        $item = $service->createHandoverItem($resignation, 'Dokumentasi sistem');

        $this->expectException(LogicException::class);
        $service->updateHandover($item, $user, 'completed');
    }

    public function test_completion_requires_clearance_and_last_working_date_but_not_payroll(): void
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

    public function test_completion_deactivates_account_and_closes_contract_without_payroll_link(): void
    {
        Date::setTestNow('2026-01-01');
        [$user, $employee, $contract] = $this->makeEmployee();

        $resignation = app(ResignationService::class)->create(
            employee: $employee->refresh(),
            submittedBy: $user,
            proposedLastWorkingDate: '2026-01-01',
            reason: 'Alasan.',
        );

        $resignation = app(ResignationService::class)->approve(
            resignation: $resignation,
            reviewer: $user,
        );

        EmployeeResignationClearance::query()
            ->where('resignation_id', $resignation->id)
            ->update([
                'status' => 'completed',
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

        // Clearance organisasi yang dibuat sebelum refactor adalah arsip non-blocking.
        EmployeeResignationClearance::create([
            'resignation_id' => $resignation->id,
            'category' => 'organization',
            'status' => 'pending',
        ]);

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
        $this->assertDatabaseHas('employee_resignation_clearances', [
            'resignation_id' => $resignation->id,
            'category' => 'organization',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('employee_status_histories', [
            'employee_id' => $employee->id,
            'old_status' => 'active',
            'new_status' => 'resign',
            'effective_date' => '2026-01-01',
        ]);
    }
}
