<?php

namespace App\Livewire\Page\Main\Resignation;

use App\Models\EmployeeResignation;
use App\Models\EmployeeResignationClearance;
use App\Models\EmployeeResignationHandoverItem;
use App\Models\Employees;
use App\Service\ResignationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Resignation'])]
class DetailResignation extends Component
{
    public EmployeeResignation $resignation;

    public string $actionNote = '';
    public string $exitInterviewNotes = '';
    public string $handoverTitle = '';
    public string $handoverDescription = '';
    public array $handoverRecipients = [];
    public array $handoverNotes = [];

    public function mount(EmployeeResignation $resignation): void
    {
        $this->resignation = $resignation->load([
            'employee.user',
            'employee.position',
            'employeeContract',
            'reviewer',
            'histories.actor',
            'clearances.verifier',
            'handoverItems.handoverTo.user',
            'exitInterviewer',
        ]);

        $this->exitInterviewNotes = $this->resignation->exit_interview_notes ?? '';
        $this->syncHandoverRecipients();
    }

    private function syncHandoverRecipients(): void
    {
        foreach ($this->resignation->handoverItems as $item) {
            $this->handoverRecipients[$item->id] = $item->handover_to_employee_id
                ? (string) $item->handover_to_employee_id
                : '';
            $this->handoverNotes[$item->id] = $item->notes ?? '';
        }
    }

    public function addHandoverItem(): void
    {
        abort_unless(Auth::user()->can('manage-resignation-clearance'), 403);

        $this->validate([
            'handoverTitle' => ['required', 'string', 'max:255'],
            'handoverDescription' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->createHandoverItem(
                resignation: $this->resignation,
                title: $this->handoverTitle,
                description: $this->handoverDescription,
            );

            $this->reset('handoverTitle', 'handoverDescription');
            $this->reload();
            session()->flash('success', 'Item handover berhasil ditambahkan.');
        } catch (\LogicException $exception) {
            $this->addError('handover', $exception->getMessage());
        }
    }

    public function approve(): void
    {
        abort_unless(Auth::user()->can('approve-resignation'), 403);

        $this->validate([
            'actionNote' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->approve(
                resignation: $this->resignation,
                reviewer: Auth::user(),
                note: $this->actionNote ?: null,
            );

            $this->actionNote = '';
            $this->reload();
            session()->flash('success', 'Pengajuan resign berhasil disetujui.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function reject(): void
    {
        abort_unless(Auth::user()->can('reject-resignation'), 403);

        $this->validate([
            'actionNote' => ['required', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->reject(
                resignation: $this->resignation,
                reviewer: Auth::user(),
                reason: $this->actionNote,
            );

            $this->actionNote = '';
            $this->reload();
            session()->flash('success', 'Pengajuan resign ditolak.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function cancel(): void
    {
        abort_unless(Auth::user()->can('cancel-resignation'), 403);

        try {
            app(ResignationService::class)->cancel(
                resignation: $this->resignation,
                actor: Auth::user(),
            );

            $this->reload();
            session()->flash('success', 'Pengajuan resign dibatalkan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function restoreAccount(): void
    {
        abort_unless(Auth::user()->can('manage-resignation-clearance'), 403);

        try {
            app(ResignationService::class)->restoreAccount(
                resignation: $this->resignation,
                actor: Auth::user(),
            );

            $this->reload();
            session()->flash('success', 'Akun employee berhasil dipulihkan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function updateClearance(int $clearanceId, string $status): void
    {
        abort_unless(Auth::user()->can('manage-resignation-clearance'), 403);

        $clearance = EmployeeResignationClearance::query()
            ->where('resignation_id', $this->resignation->id)
            ->findOrFail($clearanceId);

        try {
            app(ResignationService::class)->updateClearance(
                clearance: $clearance,
                verifier: Auth::user(),
                status: $status,
                notes: null,
            );

            $this->reload();
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function updateHandover(int $itemId, string $status): void
    {
        abort_unless(Auth::user()->can('manage-resignation-clearance'), 403);

        $item = EmployeeResignationHandoverItem::query()
            ->where('resignation_id', $this->resignation->id)
            ->findOrFail($itemId);

        try {
            app(ResignationService::class)->updateHandover(
                item: $item,
                verifier: Auth::user(),
                status: $status,
                handoverToEmployeeId: is_numeric($this->handoverRecipients[$itemId] ?? null)
                    ? (int) $this->handoverRecipients[$itemId] : null,
                notes: $this->handoverNotes[$itemId] ?? null,
            );

            $this->reload();
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function saveExitInterview(): void
    {
        abort_unless(Auth::user()->can('manage-resignation-clearance'), 403);

        $this->validate([
            'exitInterviewNotes' => ['required', 'string', 'max:10000'],
        ]);

        try {
            app(ResignationService::class)->updateExitInterview(
                resignation: $this->resignation,
                actor: Auth::user(),
                notes: $this->exitInterviewNotes,
            );

            $this->reload();
            session()->flash('success', 'Exit interview berhasil disimpan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function complete(): void
    {
        abort_unless(Auth::user()->can('complete-resignation'), 403);

        try {
            app(ResignationService::class)->complete(
                resignation: $this->resignation,
                actor: Auth::user(),
            );

            $this->reload();
            session()->flash('success', 'Proses resignation berhasil diselesaikan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function reload(): void
    {
        $this->resignation = EmployeeResignation::query()
            ->with([
                'employee.user',
                'employee.position',
                'employeeContract',
                'reviewer',
                'histories.actor',
                'clearances.verifier',
                'handoverItems.handoverTo.user',
                    'exitInterviewer',
            ])
            ->findOrFail($this->resignation->id);

        $this->exitInterviewNotes = $this->resignation->exit_interview_notes ?? '';
        $this->syncHandoverRecipients();
    }

    public function readiness(): array
    {
        return app(ResignationService::class)->readiness($this->resignation);
    }

    public function render()
    {
        $handoverEmployees = Employees::query()
            ->where('status_employee', 'active')
            ->where('id', '!=', $this->resignation->employee_id)
            ->with('user')
            ->orderBy('employee_code')
            ->get()
            ->mapWithKeys(fn (Employees $employee) => [
                $employee->id => ($employee->user?->name ?? '—') . ' · ' . ($employee->employee_code ?? '—'),
            ])
            ->all();

        return view(
            'livewire.page.main.resignation.detail-resignation',
            compact('handoverEmployees')
        );
    }
}
