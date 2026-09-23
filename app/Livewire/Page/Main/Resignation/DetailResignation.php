<?php

namespace App\Livewire\Page\Main\Resignation;

use App\Models\EmployeeResignation;
use App\Models\EmployeeResignationClearance;
use App\Models\EmployeeResignationHandoverItem;
use App\Models\Payroll;
use App\Models\Employees;
use App\Service\ResignationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Resignation'])]
class DetailResignation extends Component
{
    public EmployeeResignation $resignation;

    public string $approvedLastWorkingDate = '';
    public string $rejectionReason = '';
    public string $actionNote = '';
    public string $selectedPayrollId = '';
    public string $exitInterviewNotes = '';
    public array $handoverRecipients = [];

    public function mount(EmployeeResignation $resignation): void
    {
        $this->resignation = $resignation->load([
            'employee.user',
            'employee.position',
            'employee.team.divisi',
            'employeeContract',
            'reviewer',
            'histories.actor',
            'clearances.verifier',
            'handoverItems.task',
            'handoverItems.handoverTo.user',
            'finalPayrolls.period',
            'exitInterviewer',
        ]);

        $this->approvedLastWorkingDate = $this->resignation->approved_last_working_date?->toDateString()
            ?? $this->resignation->proposed_last_working_date?->toDateString()
            ?? today()->toDateString();
        $this->exitInterviewNotes = $this->resignation->exit_interview_notes ?? '';
        $this->syncHandoverRecipients();
    }

    private function syncHandoverRecipients(): void
    {
        foreach ($this->resignation->handoverItems as $item) {
            $this->handoverRecipients[$item->id] = $item->handover_to_employee_id
                ? (string) $item->handover_to_employee_id
                : '';
        }
    }

    public function approve(): void
    {
        $this->validate([
            'approvedLastWorkingDate' => ['required', 'date', 'after_or_equal:today'],
            'actionNote' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->approve(
                resignation: $this->resignation,
                reviewer: Auth::user(),
                approvedLastWorkingDate: $this->approvedLastWorkingDate,
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
        $this->validate([
            'rejectionReason' => ['required', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->reject(
                resignation: $this->resignation,
                reviewer: Auth::user(),
                reason: $this->rejectionReason,
            );

            $this->rejectionReason = '';
            $this->reload();
            session()->flash('success', 'Pengajuan resign ditolak.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function cancel(): void
    {
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

    public function updateClearance(int $clearanceId, string $status): void
    {
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
        $item = EmployeeResignationHandoverItem::query()
            ->where('resignation_id', $this->resignation->id)
            ->findOrFail($itemId);

        try {
            app(ResignationService::class)->updateHandover(
                item: $item,
                verifier: Auth::user(),
                status: $status,
                handoverToEmployeeId: $this->handoverRecipients[$itemId] ?? null,
                notes: null,
            );

            $this->reload();
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function saveExitInterview(): void
    {
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

    public function linkFinalPayroll(): void
    {
        $this->validate([
            'selectedPayrollId' => ['required', 'integer'],
        ]);

        $payroll = Payroll::query()
            ->where('employee_id', $this->resignation->employee_id)
            ->whereKey($this->selectedPayrollId)
            ->firstOrFail();

        try {
            app(ResignationService::class)->linkFinalPayroll(
                resignation: $this->resignation,
                payroll: $payroll,
                actor: Auth::user(),
            );

            $this->selectedPayrollId = '';
            $this->reload();
            session()->flash('success', 'Payroll akhir berhasil dihubungkan.');
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function complete(): void
    {
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
                'employee.team.divisi',
                'employeeContract',
                'reviewer',
                'histories.actor',
                'clearances.verifier',
                'handoverItems.task',
                'handoverItems.handoverTo.user',
                'finalPayrolls.period',
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
        $payrolls = Payroll::query()
            ->with('period')
            ->where('employee_id', $this->resignation->employee_id)
            ->where('status', 'paid')
            ->latest('id')
            ->get();

        $handoverEmployees = Employees::query()
            ->where('status_employee', 'active')
            ->whereKeyNot($this->resignation->employee_id)
            ->with('user')
            ->orderBy('employee_code')
            ->get()
            ->mapWithKeys(fn (Employees $employee) => [
                $employee->id => ($employee->user?->name ?? '—') . ' · ' . ($employee->employee_code ?? '—'),
            ])
            ->all();

        return view(
            'livewire.page.main.resignation.detail-resignation',
            compact('payrolls', 'handoverEmployees')
        );
    }
}
