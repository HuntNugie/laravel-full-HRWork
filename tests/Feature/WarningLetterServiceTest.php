<?php

namespace Tests\Feature;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Models\User;
use App\Models\WarningLetterSequence;
use App\Service\WarningLetterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarningLetterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_draft_generates_unique_number_and_persists_draft(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Ketidakhadiran tanpa keterangan.',
                'description' => 'Tiga hari unpresent.',
            ],
            createdBy: $user->id,
        );

        $this->assertSame('draft', $warningLetter->status);
        $this->assertSame('SP/2026/09/001', $warningLetter->letter_number);
        $this->assertSame($user->id, $warningLetter->created_by);
        $this->assertDatabaseHas('warning_letter_sequences', [
            'year' => 2026,
            'month' => 9,
            'last_number' => 1,
        ]);
    }

    public function test_number_generation_increments_within_same_month(): void
    {
        $service = app(WarningLetterService::class);

        $this->assertSame(
            'SP/2026/09/001',
            $service->generateWarningLetterNumber()
        );

        $this->assertSame(
            'SP/2026/09/002',
            $service->generateWarningLetterNumber()
        );

        $this->assertSame(
            'SP/2026/09/003',
            $service->previewWarningLetterNumber()
        );
    }

    public function test_draft_can_be_updated_but_existing_number_is_preserved(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Alasan awal.',
                'description' => null,
            ],
            createdBy: $user->id,
        );

        $updated = app(WarningLetterService::class)->updateDraft(
            warningLetter: $warningLetter,
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP2',
                'issued_date' => '2026-09-22',
                'reason' => 'Alasan diperbarui.',
                'description' => 'Keterangan diperbarui.',
            ],
        );

        $this->assertSame('draft', $updated->status);
        $this->assertSame('SP/2026/09/001', $updated->letter_number);
        $this->assertSame('SP2', $updated->warning_level);
        $this->assertSame('Alasan diperbarui.', $updated->reason);
        $this->assertSame('Keterangan diperbarui.', $updated->description);
    }

    public function test_only_draft_can_be_issued(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Pelanggaran disiplin.',
                'description' => null,
            ],
            createdBy: $user->id,
        );

        $issued = app(WarningLetterService::class)->issue(
            warningLetter: $warningLetter,
            issuedBy: $user->id,
        );

        $this->assertSame('issued', $issued->status);
        $this->assertSame($user->id, $issued->issued_by);
        $this->assertNotNull($issued->issued_at);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Surat Peringatan sudah tidak dapat diedit atau diterbitkan.'
        );

        app(WarningLetterService::class)->issue(
            warningLetter: $issued,
            issuedBy: $user->id,
        );
    }

    public function test_draft_can_be_cancelled_with_a_required_reason(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Pelanggaran disiplin.',
                'description' => null,
            ],
            createdBy: $user->id,
        );

        $cancelled = app(WarningLetterService::class)->cancel(
            warningLetter: $warningLetter,
            reason: 'Data pelanggaran perlu diperiksa kembali.',
            cancelledBy: $user->id,
        );

        $this->assertSame('cancelled', $cancelled->status);
        $this->assertSame(
            'Data pelanggaran perlu diperiksa kembali.',
            $cancelled->cancellation_reason
        );
        $this->assertSame($user->id, $cancelled->cancelled_by);
        $this->assertNotNull($cancelled->cancelled_at);
    }

    public function test_issued_letter_can_be_cancelled_but_cancelled_letter_is_terminal(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Pelanggaran disiplin.',
                'description' => null,
            ],
            createdBy: $user->id,
        );

        $issued = app(WarningLetterService::class)->issue(
            warningLetter: $warningLetter,
            issuedBy: $user->id,
        );

        $cancelled = app(WarningLetterService::class)->cancel(
            warningLetter: $issued,
            reason: 'Keputusan pembatalan HR.',
            cancelledBy: $user->id,
        );

        $this->assertSame('cancelled', $cancelled->status);
        $this->assertSame($user->id, $cancelled->issued_by);
        $this->assertNotNull($cancelled->issued_at);
        $this->assertSame($user->id, $cancelled->cancelled_by);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Surat Peringatan yang sudah dibatalkan tidak dapat dibatalkan kembali.'
        );

        app(WarningLetterService::class)->cancel(
            warningLetter: $cancelled,
            reason: 'Percobaan kedua.',
            cancelledBy: $user->id,
        );
    }

    public function test_draft_cannot_be_issued_without_reason(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = EmployeeWarningLetter::create([
            'employee_id' => $employee->id,
            'warning_level' => 'SP1',
            'letter_number' => 'SP/2026/09/999',
            'issued_date' => '2026-09-21',
            'reason' => ' ',
            'description' => null,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Alasan Surat Peringatan wajib diisi sebelum diterbitkan.'
        );

        app(WarningLetterService::class)->issue(
            warningLetter: $warningLetter,
            issuedBy: $user->id,
        );
    }

    public function test_draft_cannot_be_cancelled_without_reason(): void
    {
        $user = User::factory()->create();
        $employee = $this->createEmployee($user);

        $warningLetter = app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => $employee->id,
                'warning_level' => 'SP1',
                'issued_date' => '2026-09-21',
                'reason' => 'Pelanggaran disiplin.',
                'description' => null,
            ],
            createdBy: $user->id,
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Alasan pembatalan wajib diisi.'
        );

        app(WarningLetterService::class)->cancel(
            warningLetter: $warningLetter,
            reason: '   ',
            cancelledBy: $user->id,
        );
    }

    private function createEmployee(User $user): Employees
    {
        return Employees::create([
            'employee_code' => 'EMP-' . uniqid(),
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
        ]);
    }
}
