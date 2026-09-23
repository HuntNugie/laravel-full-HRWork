<?php

namespace App\Service;

use App\Models\EmployeeResignation;
use App\Models\EmployeeTermination;
use App\Models\EmployeeTerminationClearance;
use App\Models\EmployeeTerminationHandoverItem;
use App\Models\EmployeeTerminationHistory;
use App\Models\Employees;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class TerminationService
{
    private const CLEARANCE_CATEGORIES = [
        'work',
        'asset',
        'finance',
        'document',
        'access',
        'organization',
    ];

    private const REASON_TYPES = [
        'performance',
        'disciplinary',
        'restructuring',
        'efficiency',
        'attendance',
        'other',
    ];

    public function reasonTypes(): array
    {
        return [
            'performance' => 'Kinerja',
            'disciplinary' => 'Pelanggaran Disiplin',
            'restructuring' => 'Restrukturisasi',
            'efficiency' => 'Efisiensi',
            'attendance' => 'Ketidakhadiran',
            'other' => 'Lainnya',
        ];
    }

    public function create(
        Employees $employee,
        User $initiatedBy,
        string $effectiveDate,
        string $reasonType,
        string $reason,
        ?string $notes = null,
    ): EmployeeTermination {
        $date = Carbon::parse($effectiveDate)->startOfDay();
        $reasonType = trim($reasonType);
        $reason = trim($reason);

        return DB::transaction(function () use (
            $employee,
            $initiatedBy,
            $date,
            $reasonType,
            $reason,
            $notes,
        ) {
            $employee = Employees::query()
                ->with('employeeContract')
                ->lockForUpdate()
                ->findOrFail($employee->id);

            if ($employee->status_employee !== 'active') {
                throw new LogicException('Hanya karyawan aktif yang dapat diproses PHK.');
            }

            if ($date->lt(today())) {
                throw new LogicException('Tanggal efektif PHK tidak boleh sebelum hari ini.');
            }

            if (!in_array($reasonType, self::REASON_TYPES, true)) {
                throw new LogicException('Jenis alasan PHK tidak valid.');
            }

            if ($reason === '') {
                throw new LogicException('Detail alasan PHK wajib diisi.');
            }

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

            if (!$contract) {
                throw new LogicException('Karyawan harus memiliki contract aktif.');
            }

            $hasActiveResignation = $employee->resignations()
                ->whereIn('status', [
                    EmployeeResignation::STATUS_SUBMITTED,
                    EmployeeResignation::STATUS_APPROVED,
                ])
                ->exists();

            if ($hasActiveResignation) {
                throw new LogicException('Karyawan sedang memiliki proses resignation yang masih berjalan.');
            }

            $hasActiveTermination = $employee->terminations()
                ->where('status', EmployeeTermination::STATUS_IN_PROGRESS)
                ->exists();

            if ($hasActiveTermination) {
                throw new LogicException('Karyawan sudah memiliki proses PHK yang masih berjalan.');
            }

            $termination = EmployeeTermination::query()->create([
                'employee_id' => $employee->id,
                'employee_contract_id' => $contract->id,
                'initiated_by' => $initiatedBy->id,
                'initiated_at' => now(),
                'effective_date' => $date->toDateString(),
                'reason_type' => $reasonType,
                'reason' => $reason,
                'notes' => $notes ? trim($notes) : null,
                'status' => EmployeeTermination::STATUS_IN_PROGRESS,
            ]);

            $this->prepareHandover($termination);
            $this->prepareClearances($termination, $initiatedBy);

            $this->recordHistory(
                termination: $termination,
                actor: $initiatedBy,
                fromStatus: null,
                toStatus: EmployeeTermination::STATUS_IN_PROGRESS,
                note: 'Proses PHK dibuat langsung oleh HR.',
            );

            return $termination->refresh();
        });
    }

    public function cancel(
        EmployeeTermination $termination,
        User $actor,
        string $reason,
    ): EmployeeTermination {
        $reason = trim($reason);

        if ($reason === '') {
            throw new LogicException('Alasan pembatalan PHK wajib diisi.');
        }

        return DB::transaction(function () use ($termination, $actor, $reason) {
            $termination = EmployeeTermination::query()
                ->with(['clearances', 'handoverItems'])
                ->lockForUpdate()
                ->findOrFail($termination->id);

            if ($termination->status !== EmployeeTermination::STATUS_IN_PROGRESS) {
                throw new LogicException('Proses PHK ini tidak dapat dibatalkan.');
            }

            $exitActions = app(EmployeeExitActionService::class);

            foreach ($termination->handoverItems as $item) {
                $exitActions->rollbackHandover($item);
            }

            foreach ($termination->clearances as $clearance) {
                $exitActions->rollbackClearance($clearance);
            }

            $fromStatus = $termination->status;

            $termination->update([
                'status' => EmployeeTermination::STATUS_CANCELLED,
                'cancelled_by' => $actor->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            $this->recordHistory(
                termination: $termination,
                actor: $actor,
                fromStatus: $fromStatus,
                toStatus: EmployeeTermination::STATUS_CANCELLED,
                note: $reason,
            );

            return $termination->refresh();
        });
    }

    public function updateClearance(
        EmployeeTerminationClearance $clearance,
        User $verifier,
        string $status,
        ?string $notes = null,
    ): EmployeeTerminationClearance {
        if (!in_array($status, [
            EmployeeTerminationClearance::STATUS_PENDING,
            EmployeeTerminationClearance::STATUS_COMPLETED,
            EmployeeTerminationClearance::STATUS_NOT_APPLICABLE,
        ], true)) {
            throw new LogicException('Status clearance tidak valid.');
        }

        return DB::transaction(function () use ($clearance, $verifier, $status, $notes) {
            $clearance = EmployeeTerminationClearance::query()
                ->lockForUpdate()
                ->findOrFail($clearance->id);

            $termination = $clearance->termination()->lockForUpdate()->firstOrFail();

            if ($termination->status !== EmployeeTermination::STATUS_IN_PROGRESS) {
                throw new LogicException('Clearance hanya dapat diproses pada PHK yang sedang berjalan.');
            }

            $previousStatus = $clearance->status;

            if (
                in_array($previousStatus, [
                    EmployeeTerminationClearance::STATUS_COMPLETED,
                    EmployeeTerminationClearance::STATUS_NOT_APPLICABLE,
                ], true)
                && $status !== EmployeeTerminationClearance::STATUS_COMPLETED
            ) {
                app(EmployeeExitActionService::class)->rollbackClearance($clearance);
            }

            $clearance->update([
                'status' => $status,
                'notes' => $notes ? trim($notes) : null,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
            ]);

            if ($status === EmployeeTerminationClearance::STATUS_COMPLETED) {
                app(EmployeeExitActionService::class)->applyClearance(
                    clearance: $clearance->refresh(),
                    actor: $verifier,
                );
            }

            return $clearance->refresh();
        });
    }

    public function updateHandover(
        EmployeeTerminationHandoverItem $item,
        User $verifier,
        string $status,
        ?int $handoverToEmployeeId = null,
        ?string $notes = null,
    ): EmployeeTerminationHandoverItem {
        if (!in_array($status, [
            EmployeeTerminationHandoverItem::STATUS_PENDING,
            EmployeeTerminationHandoverItem::STATUS_IN_PROGRESS,
            EmployeeTerminationHandoverItem::STATUS_COMPLETED,
            EmployeeTerminationHandoverItem::STATUS_NOT_APPLICABLE,
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
            $item = EmployeeTerminationHandoverItem::query()
                ->lockForUpdate()
                ->findOrFail($item->id);

            $termination = $item->termination()->lockForUpdate()->firstOrFail();

            if ($termination->status !== EmployeeTermination::STATUS_IN_PROGRESS) {
                throw new LogicException('Handover hanya dapat diproses pada PHK yang sedang berjalan.');
            }

            $previousStatus = $item->status;

            if ($handoverToEmployeeId !== null) {
                $employee = Employees::query()->find($handoverToEmployeeId);

                if (!$employee || $employee->status_employee !== 'active') {
                    throw new LogicException('Employee penerima handover harus berstatus aktif.');
                }
            }

            if (
                $status === EmployeeTerminationHandoverItem::STATUS_COMPLETED
                && $item->task_id !== null
                && $handoverToEmployeeId === null
            ) {
                throw new LogicException('Penerima handover wajib ditentukan sebelum pekerjaan diselesaikan.');
            }

            if (
                $previousStatus === EmployeeTerminationHandoverItem::STATUS_COMPLETED
                && $status !== EmployeeTerminationHandoverItem::STATUS_COMPLETED
            ) {
                app(EmployeeExitActionService::class)->rollbackHandover($item);
            }

            $item->update([
                'status' => $status,
                'handover_to_employee_id' => $handoverToEmployeeId,
                'notes' => $notes ? trim($notes) : null,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
            ]);

            if ($status === EmployeeTerminationHandoverItem::STATUS_COMPLETED) {
                app(EmployeeExitActionService::class)->applyHandover(
                    item: $item->refresh(),
                    actor: $verifier,
                );
            }

            return $item->refresh();
        });
    }

    public function complete(
        EmployeeTermination $termination,
        User $actor,
    ): EmployeeTermination {
        return DB::transaction(function () use ($termination, $actor) {
            $termination = EmployeeTermination::query()
                ->with([
                    'employee',
                    'employeeContract',
                    'clearances',
                    'handoverItems',
                ])
                ->lockForUpdate()
                ->findOrFail($termination->id);

            if ($termination->status !== EmployeeTermination::STATUS_IN_PROGRESS) {
                throw new LogicException('Hanya PHK yang sedang berjalan yang dapat diselesaikan.');
            }

            $effectiveDate = $termination->effective_date;

            if (!$effectiveDate || Carbon::parse($effectiveDate)->isFuture()) {
                throw new LogicException('Tanggal efektif PHK belum tercapai.');
            }

            $this->ensureClearanceCompleted($termination);
            $this->ensureHandoverCompleted($termination);

            if ($this->hasUnresolvedLeave($termination)) {
                throw new LogicException('Masih ada cuti pending atau approved yang melewati tanggal efektif PHK.');
            }

            $employee = Employees::query()
                ->with('user')
                ->lockForUpdate()
                ->findOrFail($termination->employee_id);

            if ($employee->status_employee !== 'active') {
                throw new LogicException('Employee sudah tidak aktif sehingga PHK tidak dapat diselesaikan.');
            }

            $employee->supervisorTeam()->update([
                'supervisor_id' => null,
            ]);

            $employee->managedDivisi()->update([
                'manager_id' => null,
            ]);

            $employee->update([
                'status_employee' => 'terminated',
                'TerminationDate' => Carbon::parse($effectiveDate)->toDateString(),
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
                $contract->update([
                    'status' => 'terminated',
                ]);
            }

            $employee->statusHistory()->create([
                'old_status' => 'active',
                'new_status' => 'terminated',
                'effective_date' => Carbon::parse($effectiveDate)->toDateString(),
                'reason' => $termination->reason,
            ]);

            $termination->update([
                'status' => EmployeeTermination::STATUS_COMPLETED,
                'completed_by' => $actor->id,
                'completed_at' => now(),
            ]);

            $this->recordHistory(
                termination: $termination,
                actor: $actor,
                fromStatus: EmployeeTermination::STATUS_IN_PROGRESS,
                toStatus: EmployeeTermination::STATUS_COMPLETED,
                note: 'Proses PHK selesai.',
            );

            return $termination->refresh();
        });
    }

    public function readiness(EmployeeTermination $termination): array
    {
        $termination->loadMissing([
            'clearances',
            'handoverItems',
            'employee',
        ]);

        $clearanceReady = $termination->clearances->every(
            fn (EmployeeTerminationClearance $clearance) =>
                in_array($clearance->status, [
                    EmployeeTerminationClearance::STATUS_COMPLETED,
                    EmployeeTerminationClearance::STATUS_NOT_APPLICABLE,
                ], true)
        );

        $handoverReady = $termination->handoverItems->every(
            fn (EmployeeTerminationHandoverItem $item) =>
                in_array($item->status, [
                    EmployeeTerminationHandoverItem::STATUS_COMPLETED,
                    EmployeeTerminationHandoverItem::STATUS_NOT_APPLICABLE,
                ], true)
        );

        $organizationReady = true;

        $leaveReady = !$this->hasUnresolvedLeave($termination);

        $effectiveDateReached = $termination->effective_date
            ? Carbon::parse($termination->effective_date)->lte(today())
            : false;

        return [
            'clearance' => $clearanceReady,
            'handover' => $handoverReady,
            'organization' => $organizationReady,
            'leave' => $leaveReady,
            'effective_date' => $effectiveDateReached,
            'ready' => $effectiveDateReached
                && $clearanceReady
                && $handoverReady
                && $organizationReady
                && $leaveReady,
        ];
    }

    private function prepareClearances(
        EmployeeTermination $termination,
        User $verifier,
    ): void {
        foreach (self::CLEARANCE_CATEGORIES as $category) {
            EmployeeTerminationClearance::query()->firstOrCreate([
                'termination_id' => $termination->id,
                'category' => $category,
            ]);
        }

        $work = $termination->clearances()
            ->where('category', 'work')
            ->firstOrFail();

        if ($termination->handoverItems()->doesntExist()) {
            $work->update([
                'status' => EmployeeTerminationClearance::STATUS_COMPLETED,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
                'notes' => 'Tidak ada pekerjaan aktif yang perlu dihandover.',
            ]);
        }

    }

    private function prepareHandover(EmployeeTermination $termination): void
    {
        $tasks = Task::query()
            ->where('assignee_id', $termination->employee_id)
            ->whereNotIn('status', [
                Task::STATUS_DONE,
                Task::STATUS_CANCELLED,
            ])
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();

        foreach ($tasks as $task) {
            EmployeeTerminationHandoverItem::query()->firstOrCreate([
                'termination_id' => $termination->id,
                'task_id' => $task->id,
            ], [
                'title' => $task->title,
                'description' => $task->description,
            ]);
        }
    }

    private function ensureClearanceCompleted(EmployeeTermination $termination): void
    {
        foreach ($termination->clearances as $clearance) {
            if (!in_array($clearance->status, [
                EmployeeTerminationClearance::STATUS_COMPLETED,
                EmployeeTerminationClearance::STATUS_NOT_APPLICABLE,
            ], true)) {
                throw new LogicException("Clearance '{$clearance->category}' belum selesai.");
            }
        }
    }

    private function ensureHandoverCompleted(EmployeeTermination $termination): void
    {
        foreach ($termination->handoverItems as $item) {
            if (!in_array($item->status, [
                EmployeeTerminationHandoverItem::STATUS_COMPLETED,
                EmployeeTerminationHandoverItem::STATUS_NOT_APPLICABLE,
            ], true)) {
                throw new LogicException("Handover '{$item->title}' belum selesai.");
            }
        }
    }

    private function hasUnresolvedLeave(EmployeeTermination $termination): bool
    {
        $effectiveDate = $termination->effective_date;

        if (!$effectiveDate) {
            return true;
        }

        $date = Carbon::parse($effectiveDate)->toDateString();

        return $termination->employee?->leaveRequest()
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
        EmployeeTermination $termination,
        ?User $actor,
        ?string $fromStatus,
        string $toStatus,
        ?string $note = null,
    ): void {
        EmployeeTerminationHistory::create([
            'termination_id' => $termination->id,
            'actor_id' => $actor?->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note ? trim($note) : null,
        ]);
    }
}
