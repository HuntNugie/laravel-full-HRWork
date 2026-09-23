<?php

namespace App\Livewire\Page\Main\Termination;

use App\Models\EmployeeTermination;
use App\Models\EmployeeTerminationClearance;
use App\Models\EmployeeTerminationHandoverItem;
use App\Models\Employees;
use App\Service\TerminationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail PHK'])]
class DetailTermination extends Component
{
    public EmployeeTermination $termination;

    public string $approvedEffectiveDate = '';
    public string $actionNote = '';
    public string $cancellationReason = '';
    public array $handoverRecipients = [];

    public function mount(EmployeeTermination $termination): void
    {
        $this->termination = $termination->load([
            'employee.user',
            'employee.position',
            'employee.team.divisi',
            'employeeContract',
            'initiator',
            'reviewer',
            'canceller',
            'completer',
            'histories.actor',
            'clearances.verifier',
            'handoverItems.task',
            'handoverItems.handoverTo.user',
        ]);

        $this->approvedEffectiveDate =
            $this->termination->approved_effective_date?->toDateString()
            ?? $this->termination->proposed_effective_date?->toDateString()
            ?? today()->toDateString();

        $this->syncHandoverRecipients();
    }

    private function syncHandoverRecipients(): void
    {
        foreach ($this->termination->handoverItems as $item) {
            $this->handoverRecipients[$item->id] = $item->handover_to_employee_id
                ? (string) $item->handover_to_employee_id
                : '';
        }
    }

    public function approve(): void
    {
        abort_unless(Auth::user()->can('approve-termination'), 403);

        $this->validate([
            'approvedEffectiveDate' => ['required', 'date', 'after_or_equal:today'],
            'actionNote' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            app(TerminationService::class)->approve(
                termination: $this->termination,
                reviewer: Auth::user(),
                approvedEffectiveDate: $this->approvedEffectiveDate,
                note: $this->actionNote ?: null,
            );

            $this->actionNote = '';
            $this->reload();
            session()->flash('success', 'Pengajuan PHK berhasil disetujui.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function reject(): void
    {
        abort_unless(Auth::user()->can('reject-termination'), 403);

        $this->validate([
            'actionNote' => ['required', 'string', 'max:5000'],
        ]);

        try {
            app(TerminationService::class)->reject(
                termination: $this->termination,
                reviewer: Auth::user(),
                reason: $this->actionNote,
            );

            $this->actionNote = '';
            $this->reload();
            session()->flash('success', 'Pengajuan PHK ditolak.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function cancel(): void
    {
        abort_unless(Auth::user()->can('cancel-termination'), 403);

        $this->validate([
            'cancellationReason' => ['required', 'string', 'max:5000'],
        ]);

        try {
            app(TerminationService::class)->cancel(
                termination: $this->termination,
                actor: Auth::user(),
                reason: $this->cancellationReason,
            );

            $this->cancellationReason = '';
            $this->reload();
            session()->flash('success', 'Pengajuan PHK dibatalkan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function updateClearance(int $clearanceId, string $status): void
    {
        abort_unless(Auth::user()->can('manage-termination-clearance'), 403);

        $clearance = EmployeeTerminationClearance::query()
            ->where('termination_id', $this->termination->id)
            ->findOrFail($clearanceId);

        try {
            app(TerminationService::class)->updateClearance(
                clearance: $clearance,
                verifier: Auth::user(),
                status: $status,
            );

            $this->reload();
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function updateHandover(int $itemId, string $status): void
    {
        abort_unless(Auth::user()->can('manage-termination-clearance'), 403);

        $item = EmployeeTerminationHandoverItem::query()
            ->where('termination_id', $this->termination->id)
            ->findOrFail($itemId);

        try {
            app(TerminationService::class)->updateHandover(
                item: $item,
                verifier: Auth::user(),
                status: $status,
                handoverToEmployeeId: $this->handoverRecipients[$itemId] ?? null,
            );

            $this->reload();
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function complete(): void
    {
        abort_unless(Auth::user()->can('complete-termination'), 403);

        try {
            app(TerminationService::class)->complete(
                termination: $this->termination,
                actor: Auth::user(),
            );

            $this->reload();
            session()->flash('success', 'Proses PHK berhasil diselesaikan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function reload(): void
    {
        $this->termination = EmployeeTermination::query()
            ->with([
                'employee.user',
                'employee.position',
                'employee.team.divisi',
                'employeeContract',
                'initiator',
                'reviewer',
                'canceller',
                'completer',
                'histories.actor',
                'clearances.verifier',
                'handoverItems.task',
                'handoverItems.handoverTo.user',
            ])
            ->findOrFail($this->termination->id);

        $this->approvedEffectiveDate =
            $this->termination->approved_effective_date?->toDateString()
            ?? $this->termination->proposed_effective_date?->toDateString()
            ?? today()->toDateString();

        $this->syncHandoverRecipients();
    }

    public function readiness(): array
    {
        return app(TerminationService::class)->readiness($this->termination);
    }

    public function render()
    {
        $handoverEmployees = Employees::query()
            ->where('status_employee', 'active')
            ->where('id', '!=', $this->termination->employee_id)
            ->with('user')
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->orderBy('employee_code')
            ->get()
            ->mapWithKeys(fn (Employees $employee) => [
                $employee->id => ($employee->user?->name ?? '—') . ' · ' . ($employee->employee_code ?? '—'),
            ])
            ->all();

        $reasonTypes = app(TerminationService::class)->reasonTypes();

        return view(
            'livewire.page.main.termination.detail-termination',
            compact('handoverEmployees', 'reasonTypes'),
        );
    }
}
