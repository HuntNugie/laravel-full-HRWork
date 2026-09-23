<?php

namespace App\Service;

use App\Models\EmployeeResignation;
use App\Models\EmployeeResignationClearance;
use App\Models\EmployeeResignationHandoverItem;
use App\Models\EmployeeResignationHistory;
use App\Models\EmployeeTermination;
use App\Models\Employees;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class ResignationService
{
    private const CLEARANCE_CATEGORIES = [
        'work',
        'asset',
        'finance',
        'document',
        'access',
        'organization',
    ];

    public function create(
        Employees $employee,
        User $submittedBy,
        string $proposedLastWorkingDate,
        string $reason,
        ?string $notes = null,
    ): EmployeeResignation {
        $date = Carbon::parse($proposedLastWorkingDate)->startOfDay();
        $reason = trim($reason);

        if ($employee->status_employee !== 'active') {
            throw new LogicException('Hanya karyawan aktif yang dapat mengajukan resign.');
        }

        if ($date->lt(today())) {
            throw new LogicException('Tanggal terakhir bekerja tidak boleh sebelum hari ini.');
        }

        if ($reason === '') {
            throw new LogicException('Alasan pengunduran diri wajib diisi.');
        }

        if ($employee->employeeContract()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', today())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', today());
            })
            ->doesntExist()) {
            throw new LogicException('Karyawan harus memiliki contract aktif.');
        }

        $hasActiveResignation = $employee->resignations()
            ->whereIn('status', [
                EmployeeResignation::STATUS_SUBMITTED,
                EmployeeResignation::STATUS_APPROVED,
            ])
            ->exists();

        if ($hasActiveResignation) {
            throw new LogicException('Karyawan sudah memiliki proses resign yang masih berjalan.');
        }

        $hasActiveTermination = $employee->terminations()
            ->where('status', EmployeeTermination::STATUS_IN_PROGRESS)
            ->exists();

        if ($hasActiveTermination) {
            throw new LogicException('Karyawan sudah memiliki proses PHK yang masih berjalan.');
        }

        return DB::transaction(function () use (
            $employee,
            $submittedBy,
            $date,
            $reason,
            $notes,
        ) {
            $contract = $employee->employeeContract()
                ->where('status', 'active')
                ->whereDate('start_date', '<=', today())
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', today());
                })
                ->orderByDesc('start_date')
                ->lockForUpdate()
                ->first();

            $resignation = $employee->resignations()->create([
                'employee_contract_id' => $contract?->id,
                'submitted_by' => $submittedBy->id,
                'submitted_at' => now(),
                'proposed_last_working_date' => $date->toDateString(),
                'reason' => $reason,
                'notes' => $notes ? trim($notes) : null,
                'status' => EmployeeResignation::STATUS_SUBMITTED,
            ]);

            $this->recordHistory(
                resignation: $resignation,
                actor: $submittedBy,
                fromStatus: null,
                toStatus: EmployeeResignation::STATUS_SUBMITTED,
                note: 'Pengajuan resign dibuat.',
            );

            return $resignation->refresh();
        });
    }

    public function approve(
        EmployeeResignation $resignation,
        User $reviewer,
        string $approvedLastWorkingDate,
        ?string $note = null,
    ): EmployeeResignation {
        $date = Carbon::parse($approvedLastWorkingDate)->startOfDay();

        return DB::transaction(function () use (
            $resignation,
            $reviewer,
            $date,
            $note,
        ) {
            $resignation = EmployeeResignation::query()
                ->whereKey($resignation->id)
                ->with('employee')
                ->lockForUpdate()
                ->firstOrFail();

            if ($resignation->status !== EmployeeResignation::STATUS_SUBMITTED) {
                throw new LogicException('Pengajuan resign ini tidak sedang menunggu persetujuan.');
            }

            if (!$resignation->employee || $resignation->employee->status_employee !== 'active') {
                throw new LogicException('Karyawan sudah tidak berstatus aktif.');
            }

            $submittedDate = $resignation->submitted_at
                ? Carbon::instance($resignation->submitted_at)->startOfDay()
                : today();

            if ($date->lt($submittedDate)) {
                throw new LogicException('Tanggal terakhir bekerja tidak boleh sebelum tanggal pengajuan.');
            }

            $resignation->update([
                'status' => EmployeeResignation::STATUS_APPROVED,
                'approved_last_working_date' => $date->toDateString(),
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'notes' => trim(implode("\n", array_filter([
                    $resignation->notes,
                    $note ? trim($note) : null,
                ]))),
            ]);

            $this->prepareHandover($resignation);
            $this->prepareClearances($resignation, $reviewer);
            $this->recordHistory(
                resignation: $resignation,
                actor: $reviewer,
                fromStatus: EmployeeResignation::STATUS_SUBMITTED,
                toStatus: EmployeeResignation::STATUS_APPROVED,
                note: $note ? trim($note) : 'Pengajuan resign disetujui.',
            );

            return $resignation->refresh();
        });
    }

    public function reject(
        EmployeeResignation $resignation,
        User $reviewer,
        string $reason,
    ): EmployeeResignation {
        $reason = trim($reason);

        if ($reason === '') {
            throw new LogicException('Alasan penolakan wajib diisi.');
        }

        return DB::transaction(function () use ($resignation, $reviewer, $reason) {
            $resignation = EmployeeResignation::query()
                ->whereKey($resignation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($resignation->status !== EmployeeResignation::STATUS_SUBMITTED) {
                throw new LogicException('Hanya pengajuan yang menunggu yang dapat ditolak.');
            }

            $resignation->update([
                'status' => EmployeeResignation::STATUS_REJECTED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->recordHistory(
                resignation: $resignation,
                actor: $reviewer,
                fromStatus: EmployeeResignation::STATUS_SUBMITTED,
                toStatus: EmployeeResignation::STATUS_REJECTED,
                note: $reason,
            );

            return $resignation->refresh();
        });
    }

    public function cancel(
        EmployeeResignation $resignation,
        User $actor,
    ): EmployeeResignation {
        return DB::transaction(function () use ($resignation, $actor) {
            $resignation = EmployeeResignation::query()
                ->lockForUpdate()
                ->findOrFail($resignation->id);

            if (!in_array($resignation->status, [
                EmployeeResignation::STATUS_SUBMITTED,
                EmployeeResignation::STATUS_APPROVED,
            ], true)) {
                throw new LogicException('Pengajuan resign ini tidak dapat dibatalkan.');
            }

            $resignation->update([
                'status' => EmployeeResignation::STATUS_CANCELLED,
                'cancelled_by' => $actor->id,
                'cancelled_at' => now(),
            ]);

            $this->recordHistory(
                resignation: $resignation,
                actor: $actor,
                fromStatus: $resignation->getOriginal('status'),
                toStatus: EmployeeResignation::STATUS_CANCELLED,
                note: 'Pengajuan resign dibatalkan.',
            );

            return $resignation->refresh();
        });
    }

    public function updateClearance(
        EmployeeResignationClearance $clearance,
        User $verifier,
        string $status,
        ?string $notes = null,
    ): EmployeeResignationClearance {
        if (!in_array($status, [
            EmployeeResignationClearance::STATUS_PENDING,
            EmployeeResignationClearance::STATUS_COMPLETED,
            EmployeeResignationClearance::STATUS_NOT_APPLICABLE,
        ], true)) {
            throw new LogicException('Status clearance tidak valid.');
        }

        return DB::transaction(function () use ($clearance, $verifier, $status, $notes) {
            $clearance = EmployeeResignationClearance::query()
                ->lockForUpdate()
                ->findOrFail($clearance->id);

            $resignation = $clearance->resignation()->lockForUpdate()->firstOrFail();

            if ($resignation->status !== EmployeeResignation::STATUS_APPROVED) {
                throw new LogicException('Clearance hanya dapat diproses pada resign yang sudah disetujui.');
            }

            $clearance->update([
                'status' => $status,
                'notes' => $notes ? trim($notes) : null,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
            ]);

            return $clearance->refresh();
        });
    }

    public function updateHandover(
        EmployeeResignationHandoverItem $item,
        User $verifier,
        string $status,
        ?int $handoverToEmployeeId = null,
        ?string $notes = null,
    ): EmployeeResignationHandoverItem {
        if (!in_array($status, [
            EmployeeResignationHandoverItem::STATUS_PENDING,
            EmployeeResignationHandoverItem::STATUS_IN_PROGRESS,
            EmployeeResignationHandoverItem::STATUS_COMPLETED,
            EmployeeResignationHandoverItem::STATUS_NOT_APPLICABLE,
        ], true)) {
            throw new LogicException('Status handover tidak valid.');
        }

        return DB::transaction(function () use (
            $item,
            $verifier,
            $status,
            $handoverToEmployeeId,
            $notes,
        ) {
            $item = EmployeeResignationHandoverItem::query()
                ->lockForUpdate()
                ->findOrFail($item->id);

            $resignation = $item->resignation()->lockForUpdate()->firstOrFail();

            if ($resignation->status !== EmployeeResignation::STATUS_APPROVED) {
                throw new LogicException('Handover hanya dapat diproses pada resign yang sudah disetujui.');
            }

            if ($handoverToEmployeeId !== null) {
                $employee = Employees::query()->find($handoverToEmployeeId);

                if (!$employee || $employee->status_employee !== 'active') {
                    throw new LogicException('Employee penerima handover harus berstatus aktif.');
                }
            }

            if (
                $status === EmployeeResignationHandoverItem::STATUS_COMPLETED
                && $item->task_id !== null
                && $handoverToEmployeeId === null
            ) {
                throw new LogicException('Penerima handover wajib ditentukan sebelum pekerjaan diselesaikan.');
            }

            $item->update([
                'status' => $status,
                'handover_to_employee_id' => $handoverToEmployeeId,
                'notes' => $notes ? trim($notes) : null,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
            ]);

            return $item->refresh();
        });
    }

    public function updateExitInterview(
        EmployeeResignation $resignation,
        User $actor,
        ?string $notes,
    ): EmployeeResignation {
        return DB::transaction(function () use ($resignation, $actor, $notes) {
            $resignation = EmployeeResignation::query()
                ->lockForUpdate()
                ->findOrFail($resignation->id);

            if ($resignation->status !== EmployeeResignation::STATUS_APPROVED) {
                throw new LogicException('Exit interview hanya dapat diperbarui sebelum resignation selesai.');
            }

            $notes = trim((string) $notes);

            if ($notes === '') {
                throw new LogicException('Catatan exit interview wajib diisi.');
            }

            $resignation->update([
                'exit_interview_notes' => $notes,
                'exit_interview_by' => $actor->id,
                'exit_interview_at' => now(),
            ]);

            return $resignation->refresh();
        });
    }

    public function complete(
        EmployeeResignation $resignation,
        User $actor,
    ): EmployeeResignation {
        return DB::transaction(function () use ($resignation, $actor) {
            $resignation = EmployeeResignation::query()
                ->with([
                    'employee',
                    'employeeContract',
                    'clearances',
                    'handoverItems',
                ])
                ->lockForUpdate()
                ->findOrFail($resignation->id);

            if ($resignation->status !== EmployeeResignation::STATUS_APPROVED) {
                throw new LogicException('Hanya resign yang sudah disetujui yang dapat diselesaikan.');
            }

            $lastWorkingDate = $resignation->approved_last_working_date;

            if (!$lastWorkingDate || Carbon::parse($lastWorkingDate)->isFuture()) {
                throw new LogicException('Tanggal terakhir bekerja belum tercapai.');
            }

            $this->ensureClearanceCompleted($resignation);
            $this->ensureHandoverCompleted($resignation);
            $this->ensureLeaveResolved($resignation);

            $employee = Employees::query()
                ->with('user')
                ->lockForUpdate()
                ->findOrFail($resignation->employee_id);

            if ($employee->status_employee !== 'active') {
                throw new LogicException('Employee sudah tidak aktif sehingga resignation tidak dapat diselesaikan.');
            }

            // Lepaskan seluruh assignment organisasi secara otomatis saat exit difinalisasi.
            $employee->supervisorTeam()->update([
                'supervisor_id' => null,
            ]);

            $employee->managedDivisi()->update([
                'manager_id' => null,
            ]);

            $employee->update([
                'status_employee' => 'resign',
                'ResignDate' => Carbon::parse($lastWorkingDate)->toDateString(),
                'team_id' => null,
            ]);

            if ($employee->user) {
                $employee->user->update([
                    'status' => 'inactive',
                ]);
            }

            $activeContracts = $employee->employeeContract()
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            foreach ($activeContracts as $contract) {
                $contractEnd = $contract->end_date
                    ? Carbon::parse($contract->end_date)->startOfDay()
                    : null;

                $contract->update([
                    'status' => $contractEnd && $contractEnd->lte($lastWorkingDate)
                        ? 'expired'
                        : 'terminated',
                ]);
            }

            $oldStatus = 'active';

            $employee->statusHistory()->create([
                'old_status' => $oldStatus,
                'new_status' => 'resign',
                'effective_date' => Carbon::parse($lastWorkingDate)->toDateString(),
                'reason' => $resignation->reason,
            ]);

            $resignation->update([
                'status' => EmployeeResignation::STATUS_COMPLETED,
                'completed_by' => $actor->id,
                'completed_at' => now(),
            ]);

            $this->recordHistory(
                resignation: $resignation,
                actor: $actor,
                fromStatus: EmployeeResignation::STATUS_APPROVED,
                toStatus: EmployeeResignation::STATUS_COMPLETED,
                note: 'Proses resignation selesai.',
            );

            return $resignation->refresh();
        });
    }

    public function readiness(EmployeeResignation $resignation): array
    {
        $resignation->loadMissing([
            'clearances',
            'handoverItems',
            'employee',
        ]);

        $clearanceReady = $resignation->clearances->every(
            fn(EmployeeResignationClearance $clearance) =>
                in_array($clearance->status, [
                    EmployeeResignationClearance::STATUS_COMPLETED,
                    EmployeeResignationClearance::STATUS_NOT_APPLICABLE,
                ], true)
        );

        $handoverReady = $resignation->handoverItems->every(
            fn(EmployeeResignationHandoverItem $item) =>
                in_array($item->status, [
                    EmployeeResignationHandoverItem::STATUS_COMPLETED,
                    EmployeeResignationHandoverItem::STATUS_NOT_APPLICABLE,
                ], true)
        );

        // Assignment organisasi akan dilepas otomatis saat completion.
        $organizationReady = true;

        $leaveReady = !$this->hasUnresolvedLeave($resignation);

        $lastWorkingDateReached = $resignation->approved_last_working_date
            ? Carbon::parse($resignation->approved_last_working_date)->lte(today())
            : false;

        return [
            'clearance' => $clearanceReady,
            'handover' => $handoverReady,
            'organization' => $organizationReady,
            'leave' => $leaveReady,
            'last_working_date' => $lastWorkingDateReached,
            'ready' => $lastWorkingDateReached
                && $clearanceReady
                && $handoverReady
                && $organizationReady
                && $leaveReady,
        ];
    }

    private function prepareClearances(
        EmployeeResignation $resignation,
        User $reviewer,
    ): void {
        foreach (self::CLEARANCE_CATEGORIES as $category) {
            EmployeeResignationClearance::query()->firstOrCreate([
                'resignation_id' => $resignation->id,
                'category' => $category,
            ]);
        }

        $work = $resignation->clearances()
            ->where('category', 'work')
            ->firstOrFail();

        if ($resignation->handoverItems()->doesntExist()) {
            $work->update([
                'status' => EmployeeResignationClearance::STATUS_COMPLETED,
                'verified_by' => $reviewer->id,
                'verified_at' => now(),
                'notes' => 'Tidak ada pekerjaan aktif yang perlu dihandover.',
            ]);
        }

        $employee = $resignation->employee()->lockForUpdate()->firstOrFail();

        $resignation->clearances()
            ->where('category', 'organization')
            ->update([
                'status' => EmployeeResignationClearance::STATUS_COMPLETED,
                'verified_by' => $reviewer->id,
                'verified_at' => now(),
                'notes' => 'Assignment organisasi akan dilepas otomatis saat resignation selesai.',
            ]);
    }

    private function prepareHandover(EmployeeResignation $resignation): void
    {
        $employeeId = $resignation->employee_id;

        $tasks = Task::query()
            ->where('assignee_id', $employeeId)
            ->whereNotIn('status', [
                Task::STATUS_DONE,
                Task::STATUS_CANCELLED,
            ])
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();

        foreach ($tasks as $task) {
            EmployeeResignationHandoverItem::query()->firstOrCreate([
                'resignation_id' => $resignation->id,
                'task_id' => $task->id,
            ], [
                'title' => $task->title,
                'description' => $task->description,
            ]);
        }
    }

    private function ensureClearanceCompleted(EmployeeResignation $resignation): void
    {
        foreach ($resignation->clearances as $clearance) {
            if (!in_array($clearance->status, [
                EmployeeResignationClearance::STATUS_COMPLETED,
                EmployeeResignationClearance::STATUS_NOT_APPLICABLE,
            ], true)) {
                throw new LogicException("Clearance '{$clearance->category}' belum selesai.");
            }
        }
    }

    private function ensureHandoverCompleted(EmployeeResignation $resignation): void
    {
        foreach ($resignation->handoverItems as $item) {
            if (!in_array($item->status, [
                EmployeeResignationHandoverItem::STATUS_COMPLETED,
                EmployeeResignationHandoverItem::STATUS_NOT_APPLICABLE,
            ], true)) {
                throw new LogicException("Handover '{$item->title}' belum selesai.");
            }
        }
    }

    private function ensureLeaveResolved(EmployeeResignation $resignation): void
    {
        if ($this->hasUnresolvedLeave($resignation)) {
            throw new LogicException('Masih ada cuti pending atau approved yang melewati tanggal terakhir bekerja.');
        }
    }

    private function hasUnresolvedLeave(EmployeeResignation $resignation): bool
    {
        $lastWorkingDate = $resignation->approved_last_working_date;

        if (!$lastWorkingDate) {
            return true;
        }

        $date = Carbon::parse($lastWorkingDate)->toDateString();

        return $resignation->employee?->leaveRequest()
            ->where(function ($query) use ($date) {
                $query
                    ->where(function ($query) use ($date) {
                        $query
                            ->where('status', 'pending')
                            ->whereDate('start_date', '<=', $date);
                    })
                    ->orWhere(function ($query) use ($date) {
                        $query
                            ->where('status', 'approved')
                            ->whereDate('end_date', '>', $date);
                    });
            })
            ->exists() ?? false;
    }

    private function recordHistory(
        EmployeeResignation $resignation,
        ?User $actor,
        ?string $fromStatus,
        string $toStatus,
        ?string $note = null,
    ): void {
        EmployeeResignationHistory::create([
            'resignation_id' => $resignation->id,
            'actor_id' => $actor?->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note ? trim($note) : null,
        ]);
    }
}
